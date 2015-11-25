<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\ProductImporter as MainProductImporter;
use CronBundle\Import\ImporterInterface;

class ProductImporter extends MainProductImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'product_id';

    /** @var ClientAdapter */
    protected $client;

    public function import()
    {
        $this->init();
        $this->loadLanguageId();
        $this->collectItems();
        $this->collectItemData();
        $this->saveImportLog();
    }

    protected function loadLanguageId()
    {
        $request = $this->requestModel->getLanguageRequest();
        $data = $this->client->getRequest($request);
        if (isset($data['language_id'])) {
            $languageOuterId = $data['language_id'];
            $this->requestModel->setLanguageOuterId($languageOuterId);
        }
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