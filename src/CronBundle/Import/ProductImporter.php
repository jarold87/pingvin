<?php

namespace CronBundle\Import;

use ShoprenterBundle\Import\ResponseDataConverter\ProductDataConverter;
use ShoprenterBundle\Import\RequestModel\ProductRequestModel;
use ShoprenterBundle\Import\AllowanceValidator\ProductAllowanceValidator;
use ShoprenterBundle\Import\EntityObjectSetter\ProductEntityObjectSetter;

class ProductImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'product';

    /** @var string */
    protected $entity = 'Product';

    /** @var ProductRequestModel */
    protected $requestModel;

    /** @var ProductDataConverter */
    protected $responseDataConverter;

    /** @var ProductAllowanceValidator */
    protected $allowanceValidator;

    /** @var ProductEntityObjectSetter */
    protected $entityObjectSetter;

    /** @var ClientAdapter */
    protected $client;

    protected function init()
    {
        $this->initRequestModel();
        $this->initConverter();
        $this->initAllowanceValidator();
        $this->initCollections();
        $this->initEntityObjectSetter();
        $this->client->init();
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
}