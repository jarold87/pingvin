<?php

namespace CronBundle\Import;

use ShoprenterBundle\Import\ResponseDataConverter\OrderProductDataConverter;
use ShoprenterBundle\Import\RequestModel\OrderProductRequestModel;
use ShoprenterBundle\Import\AllowanceValidator\OrderProductAllowanceValidator;
use ShoprenterBundle\Import\EntityObjectSetter\OrderProductEntityObjectSetter;

class OrderProductImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'order_product';

    /** @var string */
    protected $entity = 'OrderProduct';

    /** @var OrderProductRequestModel */
    protected $requestModel;

    /** @var OrderProductDataConverter */
    protected $responseDataConverter;

    /** @var OrderProductAllowanceValidator */
    protected $AllowanceValidator;

    /** @var OrderProductEntityObjectSetter */
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
        $this->requestModel = new OrderProductRequestModel();
    }

    protected function initConverter()
    {
        $this->responseDataConverter = new OrderProductDataConverter();
    }

    protected function initAllowanceValidator()
    {
        $this->AllowanceValidator = new OrderProductAllowanceValidator();
    }

    protected function initEntityObjectSetter()
    {
        $this->EntityObjectSetter = new OrderProductEntityObjectSetter();
    }
}