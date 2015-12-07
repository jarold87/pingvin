<?php

namespace ShoprenterBundle\Import\Component;

use CronBundle\Import\Component\ComponentFactory;
use ShoprenterBundle\Import\Component\ClientAdapter\ClientAdapter;
use ShoprenterBundle\Import\Component\ItemListCollector\ItemListCollector;
use ShoprenterBundle\Import\Component\ItemCollector\ItemCollector;
use ShoprenterBundle\Import\Component\ResponseDataConverter\OrderProductResponseDataConverter;
use ShoprenterBundle\Import\Component\RequestModel\OrderProductRequestModel;
use ShoprenterBundle\Import\Component\AllowanceValidator\OrderProductAllowanceValidator;
use ShoprenterBundle\Import\Component\EntityObjectSetter\OrderProductEntityObjectSetter;

class OrderProductComponentFactory extends ComponentFactory
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
     * @return OrderProductRequestModel
     */
    public function getRequestModel()
    {
        if (!$this->requestModel) {
            $this->requestModel = new OrderProductRequestModel();
        }
        return parent::getRequestModel();
    }

    /**
     * @return OrderProductResponseDataConverter
     */
    public function getResponseDataConverter()
    {
        if (!$this->responseDataConverter) {
            $this->responseDataConverter = new OrderProductResponseDataConverter();
        }
        return parent::getResponseDataConverter();
    }

    /**
     * @return OrderProductAllowanceValidator
     */
    public function getAllowanceValidator()
    {
        if (!$this->allowanceValidator) {
            $this->allowanceValidator = new OrderProductAllowanceValidator();
        }
        return parent::getAllowanceValidator();
    }

    /**
     * @return OrderProductEntityObjectSetter
     */
    public function getEntityObjectSetter()
    {
        if (!$this->entityObjectSetter) {
            $this->entityObjectSetter = new OrderProductEntityObjectSetter();
        }
        return parent::getEntityObjectSetter();
    }
}