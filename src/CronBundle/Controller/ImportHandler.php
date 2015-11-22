<?php

namespace CronBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CronBundle\Import\ImportListFactory;
use CronBundle\Import\ImporterIterator;
use CronBundle\Import\ClientAdapterFactory;
use CronBundle\Service\Benchmark;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\ImportScheduleLog;

class ImportHandler extends Controller
{
    /** @var int */
    protected $userLimit = 1;

    /** @var int */
    protected $timeLimit = 40;

    /** @var string */
    protected $sleepInterval = 'PT5S';

    /** @var */
    protected $actualTime;

    /** @var */
    protected $startTime;

    /** @var EntityManager */
    protected $globalEntityManager;

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $this->startTime = microtime(true);
        for ($i = 0; $i < $this->userLimit; $i++) {
            if ($this->isInLimit()) {
                $time = new \DateTime();
                $time->sub(new \DateInterval('PT5S'));
                $this->globalEntityManager = $this->getDoctrine()->getManager('global');
                $repository = $this->globalEntityManager->getRepository('AppBundle:ImportScheduleLog');
                $query = $repository->createQueryBuilder('s')
                    ->where('s.lastFinishedImportDate < :time')
                    ->setParameter('time', $time)
                    ->addOrderBy('s.priority', 'DESC')
                    ->addOrderBy('s.lastFinishedImportDate', 'ASC')
                    ->addOrderBy('s.createDate', 'ASC')
                    ->setMaxResults(1)
                    ->getQuery();
                $schedules = $query->getResult();
                //var_dump('<pre>', $schedules); exit;
                if (!isset($schedules[0])) {
                    die('NO PROCESS');
                    return;
                }
                $schedule = $schedules[0];
                $this->runOneUserImports($schedule);
                $this->globalEntityManager->flush();
            } else {
                break;
            }
        }

        $message = round(microtime(true) - $this->startTime);
        return $this->render('CronBundle::message.html.twig', array(
            'message' => $message,
        ));
    }

    /**
     * @param ImportScheduleLog $schedule
     */
    protected function runOneUserImports(ImportScheduleLog $schedule)
    {
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

        while ($iterator->hasNextImport()) {
            if ($this->isInLimit()) {
                $importer = $iterator->getNextImport();
                $importer->setEntityManager($entityManager);
                $importer->setClient($client);
                $importer->setStartTime($this->startTime);
                $importer->setActualTime($this->actualTime);
                $importer->setTimeLimit($this->timeLimit);
                $importer->setBenchmark($this->get('benchmark'));
                $importer->import();
                //TODO Meg kell nézni, hogy hiba volt-e az importban
                //TODO Ha igen, akkor rögzíteni adatbázisban
                if ($importer->isFinishedImport()) {
                    $importIndex = $iterator->getActualImportIndex();
                    $schedule->setActualImportIndex($importIndex);
                    $schedule->setUpdateDate();
                    if (!$iterator->hasNextImport()) {
                        $schedule->setActualImportIndex(1);
                        $schedule->setLastFinishedImportDate(new \DateTime());
                        //TODO prioritást nullázni kell
                    }
                } else {
                    break;
                }
            } else {
                break;
            }
        }
    }

    /**
     * @return bool
     */
    protected function isInLimit()
    {
        $this->actualTime = round(microtime(true) - $this->startTime);
        if ($this->actualTime >= round($this->timeLimit / 2)) {
            return false;
        }
        return true;
    }
}
