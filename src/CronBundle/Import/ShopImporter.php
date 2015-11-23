<?php

namespace CronBundle\Import;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\ImportCollectionLog;
use AppBundle\Entity\ImportItemLog;
use AppBundle\Entity\ImportItemProcess;
use CronBundle\Service\Benchmark;

class ShopImporter extends Importer
{
    /** @var int */
    protected $flushItemPackageNumber = 1000;

    /** @var int */
    protected $itemProcessLimit = 10000;

    /** @var int */
    protected $countAllItemProcess = 0;

    /** @var ArrayCollection */
    protected $itemProcessCollection;

    /** @var string */
    protected $entity;

    /** @var ImportCollectionLog */
    protected $collectionLog;

    /** @var ImportItemLog */
    protected $itemLog;

    /** @var Benchmark */
    protected $benchmark;

    /** @var ArrayCollection */
    protected $existEntityCollection;

    /** @var array */
    protected $existEntityKeyByOuterId = array();

    /**
     * @param $service
     */
    public function setBenchmark($service)
    {
        $this->benchmark = $service;
    }

    /**
     * @return bool
     */
    public function isFinishedImport()
    {
        if ($this->countAllItemProcess != $this->itemProcessCollection->count()) {
            return false;
        }
        return parent::isFinishedImport();
    }

    protected function initCollections()
    {
        $this->itemProcessCollection = new ArrayCollection();
        $this->existEntityCollection = new ArrayCollection();
    }

    protected function saveCollectionItems()
    {
        $this->saveItemsToProcess();
        $this->setCollectionLogFinish();
        $this->entityManager->flush();
        $this->clearItemsToProcessCollection();
    }

    protected function saveItemsToProcess()
    {
        $items = $this->itemProcessCollection->toArray();
        foreach ($items as $item) {
            $this->entityManager->persist($item);
        }
    }

    protected function clearItemsToProcessCollection()
    {
        $this->itemProcessCollection->clear();
    }

    protected function loadItemsToProcessCollection()
    {
        $items = $this->entityManager->getRepository('AppBundle:ImportItemProcess')->findAll();
        $this->countAllItemProcess = count($items);
        if ($items) {
            $limitCounter = 0;
            foreach ($items as $item) {
                if ($limitCounter > $this->itemProcessLimit) {
                    break;
                }
                $this->itemProcessCollection->add($item);
                $limitCounter++;
            }
        }
    }

    protected function loadExistEntityCollection()
    {
        $objects = $this->entityManager->getRepository('AppBundle:' . $this->entity)->findAll();
        if ($objects) {
            $key = 0;
            foreach ($objects as $object) {
                $this->existEntityCollection->add($object);
                $this->existEntityKeyByOuterId[$object->getOuterId()] = $key;
                $key++;
            }
        }
    }

    /**
     * @return bool
     */
    protected function hasInProgressCollectionRequests()
    {
        $log = $this->entityManager->getRepository('AppBundle:ImportCollectionLog')->findOneBy(
            array('importName' => $this->importName, 'finishDate' => null)
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
        $log = $this->entityManager->getRepository('AppBundle:ImportItemLog')->findOneBy(
            array('importName' => $this->importName, 'finishDate' => null)
        );
        if (!$log) {
            return false;
        }
        $this->itemLog = $log;
        return true;
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

    /**
     * @param $index
     * @param $value
     * @return ImportItemProcess
     */
    protected function setImportItemProcess($index, $value)
    {
        $item = new ImportItemProcess();
        $item->setItemIndex($index);
        $item->setItemValue($value);
        return $item;
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
        $log->setImportName($this->importName);
        $log->setLastIndex($page);
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
        $log->setImportName($this->importName);
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
     * @param $item
     * @param $key
     */
    protected function setProcessed($item, $key)
    {
        $this->entityManager->remove($item);
        $this->itemProcessCollection->remove($key);
        $this->benchmark->processItemCount++;
    }


    protected function createImportLog()
    {
        $runtime = microtime(true) - $this->startTime;

        $this->benchmark->lastIndex = $this->getLastItemIndexFromLog();
        $this->benchmark->runtime = $runtime;
        $this->benchmark->unProcessItemCount = $this->itemProcessCollection->count();

        $log = $this->benchmark->getLog();
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}