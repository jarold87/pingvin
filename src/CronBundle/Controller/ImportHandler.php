<?php

namespace CronBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\Setting;
use GoogleApiBundle\Service\AnalyticsService;
use CronBundle\Service\ImportListFactory;
use CronBundle\Service\ImportLog;
use CronBundle\Service\RuntimeWatcher;
use CronBundle\Import\ImporterIterator;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\ImportScheduleLog;

class ImportHandler extends Controller
{
    /**
     * Egy futás alkalmával hány user importjai futhatnak
     * @var int
     */
    protected $userLimit = 1;

    /**
     * Egy futás alkalmával hány mp.-ig futhatnak az importok
     * @var int
     */
    protected $timeLimit = 45;

    /**
     * Hány hibás import futás kell ahhoz, hogy a futás leálljon
     * @var int
     */
    protected $failLimit = 6;

    /**
     * Egy usernél hány hibás import futás kell ahhoz, hogy figyelmen kívül hagyjuk a user imporjait
     * @var int
     */
    protected $failLimitPerUser = 3;

    /**
     * Milyen időközönként futhat le egy user importjai
     * 1 nap => 86400
     * @var string
     */
    protected $sleepInterval = 'PT1S';
    
    /** @var ImportLog */
    protected $importLog;

    /** @var RuntimeWatcher */
    protected $runtimeWatcher;

    /** @var EntityManager */
    protected $globalEntityManager;

    /** @var EntityManager */
    protected $userEntityManager;

    /** @var Setting */
    protected $settingService;

    /** @var AnalyticsService */
    protected $AnalyticsService;

    /** @var ImportListFactory */
    protected $importListFactoryService;

    /** @var int */
    protected $failedCounter = 0;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="cron")
     */
    public function indexAction(Request $request)
    {
        $this->runtimeWatcher = $this->get('runtimeWatcher');
        $this->runtimeWatcher->setTimeLimit($this->timeLimit);
        
        $this->importLog = $this->get('importLog');

        for ($i = 0; $i < $this->userLimit; $i++) {
            if (!$this->isInTimeLimit()) {
                $this->importLog->addMessage('reached global time limit');
                break;
            }
            if (!$this->isInFailLimit()) {
                $this->importLog->addMessage('reached global ERROR limit');
                //TODO e-mail küldés
                //valami globális hiba lehet az importban
                //célszerű lehet ilyenkor az összes importot lock-olni
                break;
            }
            $time = new \DateTime();
            $time->sub(new \DateInterval($this->sleepInterval));
            $this->globalEntityManager = $this->getDoctrine()->getManager('global');
            $repository = $this->globalEntityManager->getRepository('AppBundle:ImportScheduleLog');
            $query = $repository->createQueryBuilder('s')
                ->where('s.lastFinishedImportDate < :time')
                ->andWhere('s.failedCounter < :failed')
                ->andWhere('s.isLock = 0')
                ->setParameter('time', $time)
                ->setParameter('failed', $this->failLimitPerUser)
                ->addOrderBy('s.priority', 'DESC')
                ->addOrderBy('s.lastFinishedImportDate', 'ASC')
                ->addOrderBy('s.createDate', 'ASC')
                ->setMaxResults(1)
                ->getQuery();
            $schedules = $query->getResult();
            if (!isset($schedules[0])) {
                break;
            }
            $schedule = $schedules[0];
            $this->runOneUserImports($schedule);
        }

        $this->importLog->addMessage('run finished | ' . $this->importLog->getAllProcessItemCount());

        $this->importLog->setRuntime($this->runtimeWatcher->getRuntime());
        $log = $this->importLog->getGlobalLog();
        $this->globalEntityManager->persist($log);
        $this->globalEntityManager->flush();

        return $this->render('CronBundle::message.html.twig', array(
            'message' => $this->importLog->getMessage(),
        ));
    }

    /**
     * @param ImportScheduleLog $schedule
     * @throws \CronBundle\Import\Exception
     */
    protected function runOneUserImports(ImportScheduleLog $schedule)
    {
        $schedule->setIsLock(1);
        $this->globalEntityManager->persist($schedule);
        $this->globalEntityManager->flush();

        $this->importLog->resetUserLogData();
        $this->importLog->addMessage('user selected => ' . $schedule->getUserId());

        //TODO Biztosítani kell, hogy az adott user adatbázis kapcsolata legyen behúzva
        //Ezt most configból veszi
        $this->userEntityManager = $this->getDoctrine()->getManager('customer' . $schedule->getUserId());
        $this->userEntityManager->clear();

        $this->settingService = $this->get('setting');
        $this->settingService->setEntityManager($this->userEntityManager);
        $this->AnalyticsService = $this->get('AnalyticsService');
        $this->importListFactoryService = $this->get('ImportListFactory');

        $importIndex = $schedule->getActualImportIndex();

        $importList = $this->importListFactoryService->getImportList();
        $importList->setImporterClassNameSpace($this->getParameter('importerClassNameSpace'));
        $importList->setImporterComponentFactoryNameSpace($this->getParameter('importerComponentFactoryNameSpace'));
        $iterator = new ImporterIterator($importList);
        $iterator->setActualImportIndex($importIndex);

        while ($iterator->hasImport()) {
            if (!$this->isInTimeLimit()) {
                $this->importLog->addMessage('reached user time limit => ' . $schedule->getUserId());
                break;
            }
            if (!$this->isInUserFailLimit($schedule)) {
                $this->importLog->addMessage('reached user ERROR limit => ' . $schedule->getUserId());
                //TODO e-mail küldés
                break;
            }

            $this->importLog->addMessage('import selected => ' . $importIndex);

            $importer = $iterator->getActualImport();
            $importer->setSettingService($this->settingService);
            $importer->setAnalyticsService($this->AnalyticsService);
            $importer->setEntityManager($this->userEntityManager);
            $importer->setImportLog($this->importLog);
            $importer->setRuntimeWatcher($this->runtimeWatcher);
            $this->importLog->addMessage('import run => ' . $importIndex);
            $importer->init();
            $importer->import();
            if ($importer->getError()) {
                $this->importLog->addMessage('import ERROR in run => ' . $importIndex);
                $importIndex = $iterator->getActualImportIndex();
                $schedule->setActualImportIndex($importIndex);
                $schedule->setUpdateDate();
                $failedCounter = $schedule->getFailedCounter() + 1;
                $schedule->setFailedCounter($failedCounter);
                $this->failedCounter++;
                continue;
            }
            $this->importLog->addMessage('import success run => ' . $importIndex . ' | ' . $this->importLog->getAllProcessItemCount());
            if (!$importer->isFinishedImport()) {
                $iterator->setActualImportIndex($importIndex);
                $this->importLog->addMessage('import NOT finished => ' . $importIndex);
                continue;
            }
            $this->importLog->addMessage('import finished => ' . $importIndex);
            $iterator->setNextImportIndex();
            $importIndex = $iterator->getActualImportIndex();
            $schedule->setActualImportIndex($importIndex);
            $schedule->setUpdateDate();
            if (!$iterator->hasImport()) {
                $schedule->setActualImportIndex(1);
                $schedule->setLastFinishedImportDate(new \DateTime());
                $schedule->setPriority(0);
                $this->importLog->addMessage('all import finished => ' . $schedule->getUserId());
                continue;
            }
            $this->importLog->addMessage('all import NOT finished => ' . $schedule->getUserId());
            $this->globalEntityManager->flush();
        }
        $schedule->setIsLock(0);
        $this->globalEntityManager->persist($schedule);
        $this->globalEntityManager->flush();
    }

    /**
     * @return bool
     */
    protected function isInTimeLimit()
    {
        return $this->runtimeWatcher->isInTimeLimit();
    }

    /**
     * @return bool
     */
    protected function isInFailLimit()
    {
        if ($this->failedCounter >= $this->failLimit) {
            return false;
        }
        return true;
    }

    /**
     * @param ImportScheduleLog $schedule
     * @return bool
     */
    protected function isInUserFailLimit(ImportScheduleLog $schedule)
    {
        if ($schedule->getFailedCounter() >= $this->failLimitPerUser) {
            return false;
        }
        return true;
    }
}
