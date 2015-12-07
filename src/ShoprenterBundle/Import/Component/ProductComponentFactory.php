<?php

namespace ShoprenterBundle\Import\Component;

use CronBundle\Import\Component\ComponentFactory;
use ShoprenterBundle\Import\Component\ClientAdapter\ClientAdapter;
use ShoprenterBundle\Import\Component\ItemListCollector\ItemListCollector;
use ShoprenterBundle\Import\Component\ItemCollector\ItemCollector;
use ShoprenterBundle\Import\Component\ResponseDataConverter\ProductResponseDataConverter;
use ShoprenterBundle\Import\Component\RequestModel\ProductRequestModel;
use ShoprenterBundle\Import\Component\AllowanceValidator\ProductAllowanceValidator;
use ShoprenterBundle\Import\Component\EntityObjectSetter\ProductEntityObjectSetter;

class ProductComponentFactory extends ComponentFactory
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
     * @return ProductRequestModel
     */
    public function getRequestModel()
    {
        if (!$this->requestModel) {
            $this->requestModel = new ProductRequestModel();
        }
        return parent::getRequestModel();
    }

    /**
     * @return ProductResponseDataConverter
     */
    public function getResponseDataConverter()
    {
        if (!$this->responseDataConverter) {
            $this->responseDataConverter = new ProductResponseDataConverter();
        }
        return parent::getResponseDataConverter();
    }

    /**
     * @return ProductAllowanceValidator
     */
    public function getAllowanceValidator()
    {
        if (!$this->allowanceValidator) {
            $this->allowanceValidator = new ProductAllowanceValidator();
        }
        return parent::getAllowanceValidator();
    }

    /**
     * @return ProductEntityObjectSetter
     */
    public function getEntityObjectSetter()
    {
        if (!$this->entityObjectSetter) {
            $this->entityObjectSetter = new ProductEntityObjectSetter();
        }
        return parent::getEntityObjectSetter();
    }
}