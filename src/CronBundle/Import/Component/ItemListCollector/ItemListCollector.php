<?php

namespace CronBundle\Import\Component\ItemListCollector;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use CronBundle\Import\Component\ClientAdapter\ClientAdapter;
use CronBundle\Service\ImportLog;
use CronBundle\Service\RuntimeWatcher;
use CronBundle\Import\Component\RequestModel;
use AppBundle\Entity\ImportCollectionLog;
use AppBundle\Entity\ImportItemLog;
use AppBundle\Entity\ImportItemProcess;
use AppBundle\Entity\ImportGaRowProcess;

class ItemListCollector
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var ClientAdapter */
    protected $client;

    /** @var ImportLog */
    protected $importLog;

    /** @var RuntimeWatcher */
    protected $runtimeWatcher;

    /** @var ArrayCollection */
    protected $itemProcessCollection;

    /** @var ImportCollectionLog */
    protected $collectionLog;

    /** @var ImportItemLog */
    protected $itemLog;

    /** @var string */
    protected $importName;

    /** @var string */
    protected $outerIdKey;

    /** @var string */
    protected $entityName;

    /** @var string */
    protected $processEntityName;

    /** @var RequestModel */
    protected $requestModel;

    /** @var array */
    private $error = array();

    /** @var int */
    private $isInProgressCollectionProcess = 0;

    /** @var int */
    private $isInProgressItemProcess = 0;

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ClientAdapter $client
     */
    public function setClient(ClientAdapter $client)
    {
        $this->client = $client;
    }

    /**
     * @param ImportLog $service
     */
    public function setImportLog(ImportLog $service)
    {
        $this->importLog = $service;
    }

    /**
     * @param $runtimeWatcher
     */
    public function setRuntimeWatcher(RuntimeWatcher $runtimeWatcher)
    {
        $this->runtimeWatcher = $runtimeWatcher;
    }

    /**
     * @param $name
     */
    public function setImportName($name)
    {
        $this->importName = $name;
    }

    /**
     * @param $outerIdKey
     */
    public function setOuterIdKey($outerIdKey)
    {
        $this->outerIdKey = $outerIdKey;
    }

    /**
     * @param $entity
     */
    public function setEntityName($entity)
    {
        $this->entityName = $entity;
    }

    /**
     * @param RequestModel $requestModel
     */
    public function setRequestModel(RequestModel $requestModel)
    {
        $this->requestModel = $requestModel;
    }

    /**
     * @return array|bool
     */
    public function getError()
    {
        if ($this->error) {
            return $this->error;
        }
        return false;
    }

    /**
     * @return ImportItemLog
     */
    public function getLastItemIndex()
    {
        return $this->itemLog->getLastIndex();
    }

    public function init()
    {
        $this->itemProcessCollection = new ArrayCollection();
        $this->loadImportCollectionLog();
        $this->loadImportItemLog();
    }

    public function collect()
    {
        $this->entityManager->flush();
        $this->clearItemsToProcessCollection();
    }


    /**
     * @return bool
     */
    protected function isInProgressItemProcess()
    {
        if ($this->isInProgressItemProcess == 0) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function isInProgressCollectionProcess()
    {
        if ($this->isInProgressCollectionProcess == 0) {
            return false;
        }
        return true;
    }

    protected function loadImportCollectionLog()
    {
        $log = $this->entityManager->getRepository('AppBundle:ImportCollectionLog')->findOneBy(
            array('importName' => $this->importName, 'finishDate' => null)
        );
        if (!$log) {
            $log = new ImportCollectionLog();
            $log->setImportName($this->importName);
            $log->setLastIndex(1);
            $log->setFinishDate(new \DateTime('0000-00-00'));
        } else {
            $this->isInProgressCollectionProcess = 1;
        }
        $this->collectionLog = $log;
    }

    protected function loadImportItemLog()
    {
        $log = $this->entityManager->getRepository('AppBundle:ImportItemLog')->findOneBy(
            array('importName' => $this->importName, 'finishDate' => null)
        );
        if (!$log) {
            $log = new ImportItemLog();
            $log->setImportName($this->importName);
            $log->setLastIndex(1);
            $log->setFinishDate(new \DateTime('0000-00-00'));
        } else {
            $this->isInProgressItemProcess = 1;
        }
        $this->itemLog = $log;
    }

    /**
     * TODO ItemCollector is dolgozik a ProcessCollection-nel. Service?
     *
     * @param $list
     * @throws \Exception
     */
    protected function addToProcessCollection($list)
    {
        if ($this->processEntityName == 'ImportItemProcess') {
            $this->addItemsToProcessCollection($list);
            return;
        }
        $this->addRowsToGaProcessCollection($list);
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

    protected function addRowsToGaProcessCollection($listObject)
    {
        if (!$this->outerIdKey) {
            throw new \Exception("Not a valid outerIdKey!");
        }
        if (!$listObject->getRows()) {
            return;
        }
        foreach ($listObject->getRows() as $index => $values) {
            $dimensionKey = $values[0];
            unset($values[0]);
            $valuesString = serialize($values);
            $item = $this->setImportGaRowProcess($index + 1, $dimensionKey, $valuesString);
            $this->itemProcessCollection->add($item);
        }
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
     * @param $index
     * @param $dimensionKey
     * @param $valuesString
     * @return ImportGaRowProcess
     */
    protected function setImportGaRowProcess($index, $dimensionKey, $valuesString)
    {
        $row = new ImportGaRowProcess();
        $row->setRowIndex($index);
        $row->setDimensionKey($dimensionKey);
        $row->setRowValues($valuesString);
        return $row;
    }

    /**
     * @param $page
     */
    protected function setCollectionLogIndex($page)
    {
        $this->collectionLog->setLastIndex($page);
        $this->entityManager->persist($this->collectionLog);
    }

    protected function setCollectionLogFinish()
    {
        $this->collectionLog->setFinishDate(new \DateTime());
        $this->entityManager->persist($this->collectionLog);
    }

    /**
     * @return int
     */
    protected function getNextCollectionIndexFromLog()
    {
        return $this->collectionLog->getLastIndex() + 1;
    }

    /**
     * TODO ItemCollectorban is benne van. Service???
     * @param $index
     */
    protected function setItemLogIndex($index)
    {
        $this->itemLog->setLastIndex($index);
        $this->entityManager->persist($this->itemLog);
    }

    /**
     * @param $error
     */
    protected function addError($error)
    {
        $this->error[] = $error;
    }
}