<?php

namespace CronBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CronBundle\Import\ImportListFactory;
use CronBundle\Import\ImporterIterator;
use CronBundle\Import\ClientAdapterFactory;
use CronBundle\Service\Benchmark;

class ImportHandler extends Controller
{
    /** @var int */
    protected $userLimit = 1;

    /** @var int */
    protected $timeLimit = 50;

    /** @var */
    protected $actualTime;

    /** @var */
    protected $startTime;

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $this->startTime = microtime(true);
        $importIndex = 0;
        for ($i = 0; $i < $this->userLimit; $i++) {
            $this->actualTime = round(microtime(true) - $this->startTime);
            if ($this->actualTime < $this->timeLimit) {
                //TODO Adatbázisból lekérjük, hogy melyik user és annak melyik importja következik
                $user = 1;
                $this->runOneUserImports($user, $importIndex);
                $importIndex++;
            } else {
                break;
            }
        }

        $benchmark = $this->get('benchmark');
        $message = $benchmark->lastIndex;
        return $this->render('CronBundle::message.html.twig', array(
            'message' => $message,
        ));
    }

    /**
     * @param $user
     * @param $importIndex
     */
    protected function runOneUserImports($user, $importIndex)
    {
        //TODO Biztosítani kell, hogy az adott user adatbázis kapcsolata legyen behúzva
        $entityManager = $this->getDoctrine()->getManager('customer');

        $settingService = $this->container->get('setting');

        $factory = new ImportListFactory($settingService);
        $importList = $factory->getImportList();
        $iterator = new ImporterIterator($importList);
        $iterator->setActualImportIndex($importIndex);

        $clientFactory = new ClientAdapterFactory($settingService);
        $client = $clientFactory->getClientAdapter();

        while ($iterator->hasNextImport()) {
            $this->actualTime = round(microtime(true) - $this->startTime);
            if ($this->actualTime < $this->timeLimit) {
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
                    //TODO Adatbázisba rögzítjük a most futtatott import indexét.
                }
            } else {
                break;
            }
        }
        if (!$iterator->hasNextImport()) {
            //Ez volt a user utolsó importja
            //TODO Utolsó teljes frissítés dátumát aktualizáljuk
        }
    }
}
