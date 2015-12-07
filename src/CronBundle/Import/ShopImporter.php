<?php

namespace CronBundle\Import;

class ShopImporter extends Importer
{
    public function init()
    {
        $this->initAllowanceValidator();
        parent::init();
    }

    public function import()
    {
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
            $this->collectDeadItem();
        }

        if ($this->getError()) {
            $this->saveImportLog();
            return;
        }

        if ($this->isFinishedImport()) {
            $this->itemCollector->setItemLogFinish();
        }
        $this->saveImportLog();
    }

    protected function initClientAdapter()
    {
        parent::initClientAdapter();
        $this->clientAdapter->init();
    }

    protected function initItemCollector()
    {
        parent::initItemCollector();
        $this->itemCollector->setAllowanceValidator($this->allowanceValidator);
    }

    protected function collectDeadItem()
    {
        $this->itemCollector->collectDeadItem();
        if ($this->itemCollector->getError()) {
            $this->addError($this->itemCollector->getError());
        }
    }
}