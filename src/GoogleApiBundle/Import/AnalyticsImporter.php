<?php

namespace GoogleApiBundle\Import;

use CronBundle\Import\Importer;
use GoogleApiBundle\Import\Component\ClientAdapter\ClientAdapter;
use GoogleApiBundle\Import\Component\EntityObjectSetter\GaEntityObjectSetter;

class AnalyticsImporter extends Importer
{
    /** @var string */
    protected $timeKey;

    /** @var ClientAdapter */
    protected $clientAdapter;

    /** @var GaEntityObjectSetter */
    protected $entityObjectSetter;

    public function init()
    {
        $this->initItemProcessCollection();
        $this->initClientAdapter();
        $this->initRequestModel();
        $this->initResponseDataConverter();
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
            $this->itemCollector->setItemLogFinish();
        }
        $this->saveImportLog();
        parent::import();
    }

    protected function initClientAdapter()
    {
        parent::initClientAdapter();
        $this->clientAdapter->setAnalyticsService($this->analyticsService);
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
        $this->itemCollector->setClient($this->clientAdapter);
    }

    protected function initEntityObjectSetter()
    {
        parent::initEntityObjectSetter();
        $this->entityObjectSetter->setTimeKey($this->timeKey);
    }
}