<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\OrderProductImporter as MainOrderProductImporter;
use CronBundle\Import\ImporterInterface;

class OrderProductImporter extends MainOrderProductImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'order_product_id';

    /** @var ClientAdapter */
    protected $client;

    public function import()
    {
        $this->init();
        $this->collectItems();
        $this->collectItemData();
        $this->saveImportLog();
    }

    protected function collectItems()
    {
        if ($this->hasInProgressItemRequests()) {
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