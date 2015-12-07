<?php

namespace ShoprenterBundle\Import\Component;

use CronBundle\Import\Component\ComponentFactory;
use ShoprenterBundle\Import\Component\ClientAdapter\ClientAdapter;
use ShoprenterBundle\Import\Component\ItemListCollector\ItemListCollector;
use ShoprenterBundle\Import\Component\ItemCollector\ItemCollector;
use ShoprenterBundle\Import\Component\ResponseDataConverter\CustomerResponseDataConverter;
use ShoprenterBundle\Import\Component\RequestModel\CustomerRequestModel;
use ShoprenterBundle\Import\Component\AllowanceValidator\CustomerAllowanceValidator;
use ShoprenterBundle\Import\Component\EntityObjectSetter\CustomerEntityObjectSetter;

class CustomerComponentFactory extends ComponentFactory
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
     * @return CustomerRequestModel
     */
    public function getRequestModel()
    {
        if (!$this->requestModel) {
            $this->requestModel = new CustomerRequestModel();
        }
        return parent::getRequestModel();
    }

    /**
     * @return CustomerResponseDataConverter
     */
    public function getResponseDataConverter()
    {
        if (!$this->responseDataConverter) {
            $this->responseDataConverter = new CustomerResponseDataConverter();
        }
        return parent::getResponseDataConverter();
    }

    /**
     * @return CustomerAllowanceValidator
     */
    public function getAllowanceValidator()
    {
        if (!$this->allowanceValidator) {
            $this->allowanceValidator = new CustomerAllowanceValidator();
        }
        return parent::getAllowanceValidator();
    }

    /**
     * @return CustomerEntityObjectSetter
     */
    public function getEntityObjectSetter()
    {
        if (!$this->entityObjectSetter) {
            $this->entityObjectSetter = new CustomerEntityObjectSetter();
        }
        return parent::getEntityObjectSetter();
    }
}