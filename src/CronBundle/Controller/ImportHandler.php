<?php

namespace CronBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CronBundle\Import\ImportListFactory;
use CronBundle\Import\ImportIterator;
use CronBundle\Import\ClientAdapterFactory;

class ImportHandler extends Controller
{
    /** @var int */
    protected $clientLimit = 1;

    /** @var int */
    protected $timeLimit = 3;

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
        //Limitnek megfelelően addig végezzük az ügyfelek importjait,
        //amíg az időlimit engedi
        for ($i = 0; $i < $this->clientLimit; $i++) {
            $this->actualTime = round(microtime(true) - $this->startTime);
            if ($this->actualTime < $this->timeLimit) {
                //Adatbázisból lekérjük, hogy melyik ügyfél és annak melyik importja következik
                $user = 1;
                $importIndex = 0;
                $this->runOneClientImports($user, $importIndex);
            } else {
                break;
            }
        }


        echo '<hr>';
        return $this->render('CronBundle::message.html.twig', array(
            'message' => 'SUCCESS',
        ));
    }

    /**
     * @param $user
     * @param $importIndex
     */
    protected function runOneClientImports($user, $importIndex)
    {
        //Biztosítani kell, hogy az adott ügyfél adatbázis kapcsolata legyen behúzva

        $shopType = $this->container->getParameter('shop_type');
        $entityManager = $this->getDoctrine()->getManager('customer');

        $factory = new ImportListFactory($shopType);
        $importList = $factory->getImportList();
        $iterator = new ImportIterator($importList);
        $iterator->setActualImportIndex($importIndex);

        $settingService = $this->container->get('setting');
        $clientFactory = new ClientAdapterFactory($settingService);
        $client = $clientFactory->getClientAdapter();

        while ($iterator->hasNextImport()) {
            $this->actualTime = round(microtime(true) - $this->startTime);
            if ($this->actualTime < $this->timeLimit) {
                $import = $iterator->getNextImport();
                $import->setEntityManager($entityManager);
                $import->setClient($client);
                $import->setStartTime($this->startTime);
                $import->setActualTime($this->actualTime);
                $import->setTimeLimit($this->timeLimit);
                $import->import();
                //Meg kell nézni, hogy hiba volt-e az importban
                //Ha igen, akkor rögzíteni adatbázisban

                //Végig futott-e az adott import?
                if ($import->isFinishedImport()) {
                    $importIndex = $iterator->getActualImportIndex();
                    //Adatbázisba rögzítjük a most futtatott import indexét.
                }
                //Ellenkező esetben marad az aktuális import index
            } else {
                break;
            }
        }
        if (!$iterator->hasNextImport()) {
            //Ez volt az ügyfél utolsó importja
            //Utolsó teljes frissítés dátumát aktualizáljuk
        }
    }
}
