<?php

namespace CronBundle\Import;

use ShoprenterBundle\Import\Component\ItemListCollector\ProductCalculateMetricsItemListCollector as ItemListCollector;
use ShoprenterBundle\Import\Component\ItemCollector\ProductCalculateMetricsItemCollector as ItemCollector;

class CalculateMetricsImporter extends Importer
{
    /** @var string */
    protected $sourceEntityName;

    /** @var ItemListCollector */
    protected $itemListCollector;

    /** @var ItemCollector */
    protected $itemCollector;

    public function init()
    {
        $this->initItemProcessCollection();
        $this->initEntityObjectSetter();
        $this->initItemListCollector();
        $this->initItemCollector();
    }

    public function import()
    {
        $this->entityManager->flush();
        $this->entityManager->clear();

        if ($this->getError()) {
            $this->saveImportLog();
            return;
        }

        $this->collectItems();

        if ($this->getError()) {
            $this->saveImportLog();
            return;
        }

        $this->collectItemData();

        if ($this->getError()) {
            $this->saveImportLog();
            return;
        }

        if ($this->isFinishedImport()) {
            $this->itemCollector->setItemLogFinish();
        }
        $this->saveImportLog();
    }

    protected function initItemListCollector()
    {
        parent::initItemListCollector();
        $this->itemListCollector->setSourceEntityName($this->sourceEntityName);
    }

    protected function initItemCollector()
    {
        parent::initItemCollector();
        $this->itemCollector->setSourceEntityName($this->sourceEntityName);
    }
}