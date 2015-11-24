<?php

namespace CronBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CronBundle\Import\ImportListFactory;
use CronBundle\Import\ImporterIterator;
use CronBundle\Import\ClientAdapterFactory;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\ImportScheduleLog;

class ImportHandler extends Controller
{
    /** @var int */
    protected $userLimit = 2;

    /** @var int 45 */
    protected $timeLimit = 10;

    /** @var int */
    protected $failLimit = 6;

    /** @var int */
    protected $failLimitPerUser = 3;

    /** @var string 86400 */
    protected $sleepInterval = 'PT1S';

    /** @var float */
    protected $actualTime = 0.00;

    /** @var */
    protected $startTime;

    /** @var EntityManager */
    protected $globalEntityManager;

    /** @var int */
    protected $failedCounter = 0;

    /**
     * @Route("/", name="cron")
     */
    public function indexAction(Request $request)
    {
        $this->startTime = microtime(true);
        for ($i = 0; $i < $this->userLimit; $i++) {
            if (!$this->isInTimeLimit()) {
                $this->get('import_log')->addMessage('reached global time limit');
                break;
            }
            if (!$this->isInFailLimit()) {
                $this->get('import_log')->addMessage('reached global ERROR limit');
                break;
            }
            $time = new \DateTime();
            $time->sub(new \DateInterval($this->sleepInterval));
            $this->globalEntityManager = $this->getDoctrine()->getManager('global');
            $repository = $this->globalEntityManager->getRepository('AppBundle:ImportScheduleLog');
            $query = $repository->createQueryBuilder('s')
                ->where('s.lastFinishedImportDate < :time')
                ->andWhere('s.failedCounter < :failed')
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

        $this->get('import_log')->addMessage('run finished | ' . $this->get('import_log')->getAllProcessItemCount());

        $this->get('import_log')->setRuntime($this->actualRuntime());
        $log = $this->get('import_log')->getGlobalLog();
        $this->globalEntityManager->persist($log);
        $this->globalEntityManager->flush();

        return $this->render('CronBundle::message.html.twig', array(
            'message' => $this->get('import_log')->getMessage(),
        ));
    }

    /**
     * @param ImportScheduleLog $schedule
     * @throws \CronBundle\Import\Exception
     */
    protected function runOneUserImports(ImportScheduleLog $schedule)
    {
        $this->get('import_log')->resetUserLogData();
        $this->get('import_log')->addMessage('user selected => ' . $schedule->getUserId());

        //TODO Biztosítani kell, hogy az adott user adatbázis kapcsolata legyen behúzva
        $entityManager = $this->getDoctrine()->getManager('customer' . $schedule->getUserId());

        $settingService = $this->container->get('setting');
        $settingService->setEntityManager($entityManager);
        $importIndex = $schedule->getActualImportIndex();

        $factory = new ImportListFactory($settingService);
        $importList = $factory->getImportList();
        $iterator = new ImporterIterator($importList);
        $iterator->setActualImportIndex($importIndex);

        $clientFactory = new ClientAdapterFactory($settingService);
        $client = $clientFactory->getClientAdapter();
        $client->setImportLog($this->get('import_log'));

        while ($iterator->hasNextImport()) {
            if (!$this->isInTimeLimit()) {
                $this->get('import_log')->addMessage('reached user time limit => ' . $schedule->getUserId());
                break;
            }
            if (!$this->isInUserFailLimit($schedule)) {
                $this->get('import_log')->addMessage('reached user ERROR limit => ' . $schedule->getUserId());
                break;
            }

            $this->get('import_log')->addMessage('import selected => ' . $importIndex);

            $importer = $iterator->getActualImport();
            $importer->setEntityManager($entityManager);
            $importer->setClient($client);
            $importer->setStartTime($this->startTime);
            $importer->setActualTime($this->actualRuntime());
            $importer->setTimeLimit($this->timeLimit);
            $importer->setImportLog($this->get('import_log'));
            $this->get('import_log')->addMessage('import run => ' . $importIndex);
            $importer->import();
            if ($importer->getError()) {
                $this->get('import_log')->addMessage('import ERROR in run => ' . $importIndex);
                $importIndex = $iterator->getActualImportIndex();
                $schedule->setActualImportIndex($importIndex);
                $schedule->setUpdateDate();
                $failedCounter = $schedule->getFailedCounter() + 1;
                $schedule->setFailedCounter($failedCounter);
                $this->failedCounter++;
                continue;
            }
            $this->get('import_log')->addMessage('import success run => ' . $importIndex . ' | ' . $this->get('import_log')->getAllProcessItemCount());
            if (!$importer->isFinishedImport()) {
                $iterator->setActualImportIndex($importIndex);
                $this->get('import_log')->addMessage('import NOT finished => ' . $importIndex);
                continue;
            }
            $this->get('import_log')->addMessage('import finished => ' . $importIndex);
            $iterator->setNextImportIndex();
            $importIndex = $iterator->getActualImportIndex();
            $schedule->setActualImportIndex($importIndex);
            $schedule->setUpdateDate();
            if (!$iterator->hasNextImport()) {
                $schedule->setActualImportIndex(1);
                $schedule->setLastFinishedImportDate(new \DateTime());
                $schedule->setPriority(0);
                $this->get('import_log')->addMessage('all import finished => ' . $schedule->getUserId());
                continue;
            }
            $this->get('import_log')->addMessage('all import NOT finished => ' . $schedule->getUserId());
        }
        $this->globalEntityManager->flush();
    }

    protected function actualRuntime()
    {
        $this->actualTime = round(microtime(true) - $this->startTime, 2);
        return $this->actualTime;
    }

    /**
     * @return bool
     */
    protected function isInTimeLimit()
    {
        if ($this->actualRuntime() >= round($this->timeLimit)) {
            return false;
        }
        return true;
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
