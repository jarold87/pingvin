<?php

namespace CronBundle\Import;

use AppBundle\Entity\ImportItemProcess;
use CronBundle\Import\ItemCollector\ItemCollector;
use Doctrine\Common\Collections\ArrayCollection;

class ShopImporter extends Importer
{
    /** @var string */
    protected $entityName;

    /** @var string */
    protected $outerIdKey;

    /** @var ArrayCollection */
    protected $itemProcessCollection;

    /** @var ItemCollector */
    protected $itemCollector;

    /** @var int */
    protected $AllItemProcessCount = 0;

    /** @var int */
    protected $processedItemCount = 0;

    protected function init()
    {
        $this->initCollection();
        $this->initItemCollectorBase();
        $this->client->init();
        if ($this->client->getError()) {
            $this->addError($this->client->getError());
        }
    }

    protected function initCollection()
    {
        $this->itemProcessCollection = new ArrayCollection();
    }

    protected function initItemCollectorBase()
    {
        $this->itemCollector->setEntityManager($this->entityManager);
        $this->itemCollector->setClient($this->client);
        $this->itemCollector->setImportLog($this->importLog);
        $this->itemCollector->setEntityName($this->entityName);
        $this->itemCollector->setImportName($this->importName);
        $this->itemCollector->setOuterIdKey($this->outerIdKey);
        $this->itemCollector->setStartTime($this->startTime);
        $this->itemCollector->setRuntime($this->runtime);
        $this->itemCollector->setTimeLimit($this->timeLimit);
    }

    protected function collectItemData()
    {
        if (!$this->isInLimits()) {
            $this->timeOut = 1;
            return;
        }
        if ($this->itemLog) {
            $this->itemCollector->setItemLog($this->itemLog);
        }
        $this->itemCollector->collect();
        $this->entityManager->flush();
    }

    protected function collectDeadItem()
    {
        $this->itemCollector->collectDeadItem();
        $this->entityManager->flush();
    }

    protected function saveImportLog()
    {
        $this->importLog->setUserLastIndex($this->getLastItemIndexFromLog());
        $this->importLog->setUnProcessItemCount($this->itemCollector->getUnProcessItemCount());
        parent::saveImportLog();
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
     * @return bool
     */
    public function isFinishedImport()
    {
        if ($this->AllItemProcessCount > $this->processedItemCount) {
            return false;
        }
        return parent::isFinishedImport();
    }
}