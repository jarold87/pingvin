<?php

namespace ShoprenterBundle\Import\Component;

use CronBundle\Import\Component\ComponentFactory;
use ShoprenterBundle\Import\Component\ClientAdapter\ClientAdapter;
use ShoprenterBundle\Import\Component\ItemListCollector\ItemListCollector;
use ShoprenterBundle\Import\Component\ItemCollector\ItemCollector;
use ShoprenterBundle\Import\Component\ResponseDataConverter\OrderResponseDataConverter;
use ShoprenterBundle\Import\Component\RequestModel\OrderRequestModel;
use ShoprenterBundle\Import\Component\AllowanceValidator\OrderAllowanceValidator;
use ShoprenterBundle\Import\Component\EntityObjectSetter\OrderEntityObjectSetter;

class OrderComponentFactory extends ComponentFactory
{
    public function getClientAdapter()
    {
        if (!$this->clientAdapter) {
            $this->clientAdapter = new ClientAdapter();
        }
        return parent::getClientAdapter();
    }

    /**
     * @return ItemListCollector
     */
    public function getItemListCollector()
    {
        if (!$this->itemListCollector) {
            $this->itemListCollector = new ItemListCollector();
        }
        return parent::getItemListCollector();
    }

    /**
     * @return ItemCollector
     */
    public function getItemCollector()
    {
        if (!$this->itemCollector) {
            $this->itemCollector = new ItemCollector();
        }
        return parent::getItemCollector();
    }

    /**
     * @return OrderRequestModel
     */
    public function getRequestModel()
    {
        if (!$this->requestModel) {
            $this->requestModel = new OrderRequestModel();
        }
        return parent::getRequestModel();
    }

    /**
     * @return OrderResponseDataConverter
     */
    public function getResponseDataConverter()
    {
        if (!$this->responseDataConverter) {
            $this->responseDataConverter = new OrderResponseDataConverter();
        }
        return parent::getResponseDataConverter();
    }

    /**
     * @return OrderAllowanceValidator
     */
    public function getAllowanceValidator()
    {
        if (!$this->allowanceValidator) {
            $this->allowanceValidator = new OrderAllowanceValidator();
        }
        return parent::getAllowanceValidator();
    }

    /**
     * @return OrderEntityObjectSetter
     */
    public function getEntityObjectSetter()
    {
        if (!$this->entityObjectSetter) {
            $this->entityObjectSetter = new OrderEntityObjectSetter();
        }
        return parent::getEntityObjectSetter();
    }
}