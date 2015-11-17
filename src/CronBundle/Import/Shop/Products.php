<?php

namespace CronBundle\Import\Shop;

use CronBundle\Import\Shop\ClientAdapter;

class Products implements ImportInterface
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $entityManager;

    /** @var ClientAdapter */
    protected $client;

    /** @var */
    protected $actualTime;

    /** @var */
    protected $startTime;

    /** @var */
    protected $timeLimit;

    /** @var array */
    protected $items = array();

    /** @var array */
    protected $products = array();

    /** @var array */
    protected $processedItems = array();

    /** @var int */
    protected $timeOut = 0;

    /**
     * @param $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @param $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @param $actualTime
     */
    public function setActualTime($actualTime)
    {
        $this->actualTime = $actualTime;
    }

    /**
     * @param $timeLimit
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
    }

    public function import()
    {

    }

    /**
     * @return bool
     */
    public function isFinishedImport()
    {
        if ($this->timeOut == 1) {
            return false;
        }
        return true;
    }

    protected function emptyProcessedItems()
    {
        // Adatbázisban töröljük a már feldolgozott item-eket (import_item_process)
    }

    /**
     * @return bool
     */
    protected function hasInProgressCollectionRequests()
    {
        // Adatbázisban (import_collection_log) megnézzük, hogy van-e még folyamatban item lekérés
        // => Ha az utolsó import lognak nincs befejezés dátuma
        //return true;
        return false;
    }

    /**
     * @return bool
     */
    protected function hasInProgressItemRequests()
    {
        // Adatbázisban (import_item_log) megnézzük, hogy van-e még folyamatban item lekérés
        // => Ha az utolsó import lognak nincs befejezés dátuma
        //return true;
        return false;
    }

    protected function collectLastItems()
    {
        // Adatbázisból beolvassuk a lekérendő item-eket
        // import_item_log.last_index értéknél nagyobb item-ek lekérése az import_item_process táblából
        $lastIndex = $this->getLastItemIndexFromLog();
        $this->items = array(0 => array('href' => 'http://kgabi.api.shoprenter.hu/products/cHJvZHVjdC1wcm9kdWN0X2lkPTE2OQ=='));
    }

    /**
     * @return int
     */
    protected function getLastItemIndexFromLog()
    {
        // Adatbázisból: import_item_log.last_index
        return 0;
    }

    /**
     * @return int
     */
    protected function getNextCollectionIndexFromLog()
    {
        // Adatbázisból: import_collection_log.last_index + 1
        return 0;
    }

    protected function saveItemsToProcess()
    {
        // Adatbázisban rögzítjük a feldongozandó itemeket (import_item_process)
    }
}