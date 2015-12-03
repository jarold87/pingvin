<?php

namespace GoogleApiBundle\Import;

use GoogleApiBundle\Import\RequestModel\PageViewRequestModel;
use GoogleApiBundle\Import\ResponseDataConverter\PageViewResponseDataConverter;
use GoogleApiBundle\Import\EntityObjectSetter\ProductPageViewEntityObjectSetter;

class ProductPageViewImporter extends AnalyticsImporter
{
    /** @var string */
    protected $dimensionKey = 'Url';

    /** @var string */
    protected $timeKey;

    /** @var PageViewRequestModel */
    protected $requestModel;

    /** @var PageViewResponseDataConverter */
    protected $responseDataConverter;

    /** @var ProductPageViewEntityObjectSetter */
    protected $entityObjectSetter;

    public function import()
    {
        $this->collectItems();
        $this->collectItemData();
        $this->saveImportLog();
    }

    protected function init()
    {
        $this->initRequestModel();
        $this->initResponseDataConverter();
        $this->initEntityObjectSetter();
        parent::init();
    }

    protected function initRequestModel()
    {
        $this->requestModel = new PageViewRequestModel();
    }

    protected function initResponseDataConverter()
    {
        $this->responseDataConverter = new PageViewResponseDataConverter();
    }

    protected function initEntityObjectSetter()
    {
        $this->entityObjectSetter = new ProductPageViewEntityObjectSetter();
        $this->entityObjectSetter->setTimeKey($this->timeKey);
    }

    protected function collectItems()
    {
        if ($this->hasInProcessItemRequests()) {
            return;
        }
        $this->setCollectionLogIndex(1);
        $this->setItemLogIndex(1);
        $request = $this->requestModel->getCollectionRequest();
        $listObject = $this->client->getCollectionRequest($request);
        $this->addRowsToProcessCollection($listObject);
        $this->saveRowsToProcess();
        $this->setCollectionLogFinish();
        $this->entityManager->flush();
        $this->clearRowsToProcessCollection();
    }
}