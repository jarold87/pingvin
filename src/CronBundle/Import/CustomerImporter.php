<?php

namespace CronBundle\Import;

use ShoprenterBundle\Import\ResponseDataConverter\CustomerDataConverter;
use ShoprenterBundle\Import\RequestModel\CustomerRequestModel;
use ShoprenterBundle\Import\AllowanceValidator\CustomerAllowanceValidator;
use ShoprenterBundle\Import\EntityObjectSetter\CustomerEntityObjectSetter;

class CustomerImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'customer';

    /** @var string */
    protected $entity = 'Customer';

    /** @var CustomerRequestModel */
    protected $requestModel;

    /** @var CustomerDataConverter */
    protected $responseDataConverter;

    /** @var CustomerAllowanceValidator */
    protected $allowanceValidator;

    /** @var CustomerEntityObjectSetter */
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
        $this->requestModel = new CustomerRequestModel();
    }

    protected function initConverter()
    {
        $this->responseDataConverter = new CustomerDataConverter();
    }

    protected function initAllowanceValidator()
    {
        $this->allowanceValidator = new CustomerAllowanceValidator();
    }

    protected function initEntityObjectSetter()
    {
        $this->entityObjectSetter = new CustomerEntityObjectSetter();
    }
}