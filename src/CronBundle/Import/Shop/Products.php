<?php

namespace CronBundle\Import\Shop;

use CronBundle\Import\Shop\ClientAdapter;
use AppBundle\Entity\Product;
use AppBundle\Entity\ImportCollectionLog;
use AppBundle\Entity\ImportItemLog;
use AppBundle\Entity\ImportItemProcess;
use Symfony\Component\Validator\Constraints\DateTime;

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

    /** @var ImportCollectionLog */
    protected $collectionLog;

    /** @var ImportItemLog */
    protected $itemLog;

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
        $query = $this->entityManager->createQuery("SELECT i FROM AppBundle\Entity\ImportItemProcess i WHERE i.itemIndex >= 0");
        $items = $query->getResult();
        foreach ($items as $item) {
            $this->entityManager->remove($item);
        }
    }

    /**
     * @return bool
     */
    protected function hasInProgressCollectionRequests()
    {
        // Adatbázisban (import_collection_log) megnézzük, hogy van-e még folyamatban item lekérés
        // => Ha az utolsó import lognak nincs befejezés dátuma
        $log = $this->entityManager->getRepository('AppBundle:ImportCollectionLog')->findOneBy(
            array('importName' => 'Products', 'finishDate' => new \DateTime('0000-00-00 00:00:00'))
        );
        if (!$log) {
            return false;
        }
        $this->collectionLog = $log;
        return true;
    }

    /**
     * @return bool
     */
    protected function hasInProgressItemRequests()
    {
        // Adatbázisban (import_item_log) megnézzük, hogy van-e még folyamatban item lekérés
        // => Ha az utolsó import lognak nincs befejezés dátuma
        $query = $this->entityManager->createQuery("SELECT l FROM AppBundle\Entity\ImportItemLog l WHERE l.importName = 'Products' AND l.finishDate = '0000-00-00 00:00:00'");
        $log = $query->getResult();
        if (!$log) {
            return false;
        }
        $this->itemLog = $log[0];
        return true;
    }

    protected function collectLastItems()
    {
        // Adatbázisból beolvassuk a lekérendő item-eket
        // import_item_log.last_index értéknél nagyobb item-ek lekérése az import_item_process táblából
        $lastIndex = $this->getLastItemIndexFromLog();
        $query = $this->entityManager->createQuery("SELECT i FROM AppBundle\Entity\ImportItemProcess i WHERE i.itemIndex > " . $lastIndex);
        $items = $query->getResult();
        if (!$items) {
            return;
        }
        foreach ($items as $item) {
            $this->items[$item->getItemIndex()] = array(
                'href' => $item->getItemValue()
            );
        }
        //$this->items = array(0 => array('href' => 'http://kgabi.api.shoprenter.hu/products/cHJvZHVjdC1wcm9kdWN0X2lkPTE2OQ=='));
    }

    /**
     * @return int
     */
    protected function getLastItemIndexFromLog()
    {
        if ($this->itemLog) {
            return $this->itemLog->getLastIndex();
        }
        return 0;
    }

    /**
     * @return int
     */
    protected function getNextCollectionIndexFromLog()
    {
        if ($this->itemLog) {
            return $this->collectionLog->getLastIndex() + 1;
        }
        return 0;
    }

    protected function saveItemsToProcess()
    {
        // Adatbázisban rögzítjük a feldongozandó itemeket (import_item_process)
        if (!$this->items) {
            return;
        }
        foreach ($this->items as $key => $value) {
            $item = new ImportItemProcess();
            $item->setItemIndex($key);
            $item->setItemValue($value['href']);
            $this->entityManager->persist($item);
        }
    }

    /**
     * @param $page
     */
    protected function setCollectionLogIndex($page)
    {
        if ($this->collectionLog) {
            $this->collectionLog->setLastIndex($page);
            return;
        }
        $log = new ImportCollectionLog();
        $log->setImportName('Products');
        $log->getLastIndex($page);
        $log->setFinishDate(new \DateTime('0000-00-00'));
        $this->entityManager->persist($log);
        $this->collectionLog = $log;
    }

    protected function setCollectionLogFinish()
    {
        if ($this->collectionLog) {
            $this->collectionLog->setFinishDate(new \DateTime());
        }
    }

    /**
     * @param $index
     */
    protected function setItemLogIndex($index)
    {
        if ($this->itemLog) {
            $this->itemLog->setLastIndex($index);
            return;
        }
        $log = new ImportItemLog();
        $log->setImportName('Products');
        $log->setLastIndex($index);
        $log->setFinishDate(new \DateTime('0000-00-00'));
        $this->entityManager->persist($log);
        $this->itemLog = $log;
    }

    protected function setItemLogFinish()
    {
        if ($this->itemLog) {
            $this->itemLog->setFinishDate(new \DateTime());
        }
    }

    /**
     * @param $data
     */
    protected function setProduct($data)
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository('AppBundle:Product')->findOneBy(
            array('sku' => $data['sku'])
        );
        if ($product) {
            $product->setName($data['name']);
            $product->setPicture($data['picture']);
            $product->setUrl($data['url']);
            $product->setAvailableDate(new \DateTime($data['availableDate']));
            $product->setOuterId($data['outerId']);
            return;
        }

        $product = new Product();
        $product->setSku($data['sku']);
        $product->setName($data['name']);
        $product->setPicture($data['picture']);
        $product->setUrl($data['url']);
        $product->setAvailableDate(new \DateTime($data['availableDate']));
        $product->setOuterId($data['outerId']);
        $this->entityManager->persist($product);
    }
}