<?php

namespace CronBundle\Import;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\ImportCollectionLog;
use AppBundle\Entity\ImportItemLog;
use AppBundle\Entity\ImportItemProcess;

class ShopImporter extends Importer
{
    /** @var string */
    protected $entity;

    /** @var string */
    protected $outerIdKey;

    /** @var int 1000 */
    protected $flushItemPackageNumber = 1000;

    /** @var int 10000*/
    protected $itemProcessLimit = 10000;

    /** @var int */
    protected $countAllItemProcess = 0;

    /** @var ArrayCollection */
    protected $itemProcessCollection;

    /** @var ImportCollectionLog */
    protected $collectionLog;

    /** @var ImportItemLog */
    protected $itemLog;

    /** @var ArrayCollection */
    protected $existEntityCollection;

    /** @var array */
    protected $existEntityKeyByOuterId = array();

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

    protected function saveImportLog()
    {
        $this->importLog->setUserLastIndex($this->getLastItemIndexFromLog());
        $this->importLog->setUnProcessItemCount($this->itemProcessCollection->count());
        parent::saveImportLog();
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

    /**
     * @param $items
     * @throws \Exception
     */
    protected function addItemsToProcessCollection($items)
    {
        if (!$items) {
            return;
        }
        if (!$this->outerIdKey) {
            throw new \Exception("Not a valid outerIdKey!");
        }
        foreach ($items as $index => $value) {
            $item = $this->setImportItemProcess($index + 1, $value[$this->outerIdKey]);
            $this->itemProcessCollection->add($item);
        }
    }

    protected function saveCollectionItems()
    {
        $this->saveItemsToProcess();
        $this->setCollectionLogFinish();
        $this->entityManager->flush();
        $this->clearItemsToProcessCollection();
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

    protected function setItemLogFinish()
    {
        $this->itemLog->setFinishDate(new \DateTime());
    }

    protected function saveItemsToProcess()
    {
        $items = $this->itemProcessCollection->toArray();
        if (!$items) {
            return;
        }
        foreach ($items as $item) {
            $this->entityManager->persist($item);
        }
    }

    protected function clearItemsToProcessCollection()
    {
        $this->itemProcessCollection->clear();
    }

    /**
     * @return int
     */
    protected function getLastItemIndexFromLog()
    {
        return $this->itemLog->getLastIndex();
    }

    /**
     * @return int
     */
    protected function getNextCollectionIndexFromLog()
    {
        return $this->collectionLog->getLastIndex() + 1;
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

    protected function setCollectionLogFinish()
    {
        $this->collectionLog->setFinishDate(new \DateTime());
    }

    /**
     * @param $item
     * @param $key
     */
    protected function setProcessed($item, $key)
    {
        $this->entityManager->remove($item);
        $this->itemProcessCollection->remove($key);
        $this->importLog->addProcessItemCount();
    }

    /**
     * @param $data
     * @throws \Exception
     */
    protected function validateOuterIdInData($data)
    {
        if (!isset($data['outerId']) || !$data['outerId']) {
            throw new \Exception("Not a valid key or not exist in data!");
        }
    }

    /**
     * @param $outerId
     * @return mixed
     */
    protected function getEntityObject($outerId)
    {
        if ($this->isExistEntityObject($outerId)) {
            return $this->getObjectFromExistEntityCollectionByOuterId($outerId);
        }
        return $this->newEntityObject($outerId);
    }

    /**
     * @param $outerId
     * @return mixed
     */
    protected function newEntityObject($outerId)
    {
        $className = 'AppBundle\\Entity\\' . $this->entity;
        $object = new $className();
        $object->setOuterId($outerId);
        return $object;
    }

    /**
     * @param $outerId
     * @return bool
     */
    protected function isExistEntityObject($outerId)
    {
        if (isset($this->existEntityKeyByOuterId[$outerId])) {
            return true;
        }
        return false;
    }

    /**
     * @param $outerId
     * @return mixed
     */
    protected function getObjectFromExistEntityCollectionByOuterId($outerId)
    {
        return $this->existEntityCollection->get(
            $this->existEntityKeyByOuterId[$outerId]
        );
    }
}