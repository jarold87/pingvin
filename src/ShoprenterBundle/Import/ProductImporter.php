<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\ProductImporter as MainProductImporter;
use CronBundle\Import\ImporterInterface;
use ShoprenterBundle\Import\ResponseDataConverter\ProductDataConverter;
use ShoprenterBundle\Import\RequestModel\ProductRequestModel;
use ShoprenterBundle\Import\AllowanceValidator\ProductAllowanceValidator;
use ShoprenterBundle\Import\EntityObjectSetter\ProductEntityObjectSetter;

class ProductImporter extends MainProductImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'product_id';

    /** @var ClientAdapter */
    protected $client;

    /** @var ItemCollector */
    protected $itemCollector;

    /** @var ProductRequestModel */
    protected $requestModel;

    /** @var ProductDataConverter */
    protected $responseDataConverter;

    /** @var ProductAllowanceValidator */
    protected $allowanceValidator;

    /** @var ProductEntityObjectSetter */
    protected $entityObjectSetter;

    public function import()
    {
        $this->init();

        if ($this->getError()) {
            $this->saveImportLog();
            return;
        }

        $this->loadLanguageId();

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

        $this->collectDeadItem();
        if ($this->isFinishedImport()) {
            $this->setItemLogFinish();
        }
        $this->saveImportLog();
        die('ok');
    }

    protected function init()
    {
        $this->initRequestModel();
        $this->initConverter();
        $this->initAllowanceValidator();
        $this->initEntityObjectSetter();
        $this->initItemCollector();
        parent::init();
    }

    protected function initRequestModel()
    {
        $this->requestModel = new ProductRequestModel();
    }

    protected function initConverter()
    {
        $this->responseDataConverter = new ProductDataConverter();
    }

    protected function initAllowanceValidator()
    {
        $this->allowanceValidator = new ProductAllowanceValidator();
    }

    protected function initEntityObjectSetter()
    {
        $this->entityObjectSetter = new ProductEntityObjectSetter();
    }

    protected function initItemCollector()
    {
        $this->itemCollector = new ItemCollector();
        $this->itemCollector->setRequestModel($this->requestModel);
        $this->itemCollector->setResponseDataConverter($this->responseDataConverter);
        $this->itemCollector->setAllowanceValidator($this->allowanceValidator);
        $this->itemCollector->setEntityObjectSetter($this->entityObjectSetter);
    }

    protected function loadLanguageId()
    {
        $request = $this->requestModel->getLanguageRequest();
        $data = $this->client->getRequest($request);
        if (!isset($data['language_id'])) {
            $this->addError($this->importName . ' -> not isset language_id');
        }
        $languageOuterId = $data['language_id'];
        $this->requestModel->setLanguageOuterId($languageOuterId);
    }

    protected function collectItems()
    {
        if ($this->hasInProcessItemRequests()) {
            return;
        }
        $this->setCollectionLogIndex(1);
        $request = $this->requestModel->getCollectionRequest();
        $list = $this->client->getCollectionRequest($request);
        if ($this->client->getError()) {
            $this->addError($this->client->getError());
            return;
        }
        $this->addItemsToProcessCollection($list);
        $this->saveItemsToProcess();
        $this->setItemLogIndex(1);
        $this->setCollectionLogFinish();
        $this->entityManager->flush();
        $this->clearItemsToProcessCollection();
    }
}