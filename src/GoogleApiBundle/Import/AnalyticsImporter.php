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
    }

    protected function initClientAdapter()
    {
        parent::initClientAdapter();
        $this->clientAdapter->setAnalyticsService($this->analyticsService);
        $this->clientAdapter->init();
    }

    protected function initEntityObjectSetter()
    {
        parent::initEntityObjectSetter();
        $this->entityObjectSetter->setTimeKey($this->timeKey);
    }
}