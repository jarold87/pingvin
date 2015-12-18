<?php

namespace CronBundle\Import;

class ShopImporter extends Importer
{
    public function init()
    {
        $this->initItemProcessCollection();
        $this->initClientAdapter();
        $this->initRequestModel();
        $this->initResponseDataConverter();
        $this->initAllowanceValidator();
        $this->initEntityObjectSetter();
        $this->initItemListCollector();
        $this->initItemCollector();
        if ($this->clientAdapter->getError()) {
            $this->addError($this->clientAdapter->getError());
        }
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
        parent::import();
    }

    protected function initClientAdapter()
    {
        parent::initClientAdapter();
        $this->clientAdapter->init();
    }

    protected function initItemListCollector()
    {
        parent::initItemListCollector();
        $this->itemListCollector->setRequestModel($this->requestModel);
        $this->itemListCollector->setClient($this->clientAdapter);
    }

    protected function initItemCollector()
    {
        parent::initItemCollector();
        $this->itemCollector->setRequestModel($this->requestModel);
        $this->itemCollector->setResponseDataConverter($this->responseDataConverter);
        $this->itemCollector->setAllowanceValidator($this->allowanceValidator);
        $this->itemCollector->setClient($this->clientAdapter);
    }

    protected function collectDeadItem()
    {
        $this->itemCollector->collectDeadItem();
        if ($this->itemCollector->getError()) {
            $this->addError($this->itemCollector->getError());
        }
    }
}