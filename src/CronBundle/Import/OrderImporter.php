<?php

namespace CronBundle\Import;

use ShoprenterBundle\Import\ResponseDataConverter\OrderDataConverter;
use ShoprenterBundle\Import\RequestModel\OrderRequestModel;
use ShoprenterBundle\Import\AllowanceValidator\OrderAllowanceValidator;
use ShoprenterBundle\Import\EntityObjectSetter\OrderEntityObjectSetter;

class OrderImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'order';

    /** @var string */
    protected $entity = 'Order';

    /** @var OrderRequestModel */
    protected $requestModel;

    /** @var OrderDataConverter */
    protected $responseDataConverter;

    /** @var OrderAllowanceValidator */
    protected $AllowanceValidator;

    /** @var OrderEntityObjectSetter */
    protected $EntityObjectSetter;

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
        $this->requestModel = new OrderRequestModel();
    }

    protected function initConverter()
    {
        $this->responseDataConverter = new OrderDataConverter();
    }

    protected function initAllowanceValidator()
    {
        $this->AllowanceValidator = new OrderAllowanceValidator();
    }

    protected function initEntityObjectSetter()
    {
        $this->EntityObjectSetter = new OrderEntityObjectSetter();
    }
}