<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\CustomerImporter as MainCustomerImporter;
use CronBundle\Import\ImporterInterface;

class CustomerImporter extends MainCustomerImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'customer_id';

    /** @var ClientAdapter */
    protected $client;

    public function import()
    {
        $this->init();
        $this->collectItems();
        $this->collectItemData();

        if ($this->getError()) {
            $this->saveImportLog();
            return;
        }

        $this->collectDeadItem();
        if ($this->isFinishedImport()) {
            $this->setItemLogFinish();
        }
        $this->saveImportLog();
    }

    protected function collectItems()
    {
        if ($this->hasInProcessItemRequests()) {
            return;
        }
        $this->setCollectionLogIndex(1);
        $this->setItemLogIndex(1);
        $request = $this->requestModel->getCollectionRequest();
        $list = $this->client->getCollectionRequest($request);
        $this->addItemsToProcessCollection($list);
        $this->saveItemsToProcess();
        $this->setCollectionLogFinish();
        $this->entityManager->flush();
        $this->clearItemsToProcessCollection();
    }
}