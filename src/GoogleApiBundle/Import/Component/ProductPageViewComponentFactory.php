<?php

namespace GoogleApiBundle\Import\Component;

use CronBundle\Import\Component\ComponentFactory;
use GoogleApiBundle\Import\Component\ClientAdapter\ClientAdapter;
use GoogleApiBundle\Import\Component\ItemListCollector\ItemListCollector;
use GoogleApiBundle\Import\Component\ItemCollector\ItemCollector;
use GoogleApiBundle\Import\Component\ResponseDataConverter\PageViewResponseDataConverter;
use GoogleApiBundle\Import\Component\RequestModel\PageViewRequestModel;
use GoogleApiBundle\Import\Component\EntityObjectSetter\ProductPageViewEntityObjectSetter;

class ProductPageViewComponentFactory extends ComponentFactory
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
     * @return PageViewRequestModel
     */
    public function getRequestModel()
    {
        if (!$this->requestModel) {
            $this->requestModel = new PageViewRequestModel();
        }
        return parent::getRequestModel();
    }

    /**
     * @return PageViewResponseDataConverter
     */
    public function getResponseDataConverter()
    {
        if (!$this->responseDataConverter) {
            $this->responseDataConverter = new PageViewResponseDataConverter();
        }
        return parent::getResponseDataConverter();
    }

    /**
     * @throws \Exception
     */
    public function getAllowanceValidator()
    {
        throw new \Exception('Missing AllowanceValidator!');
    }

    /**
     * @return ProductPageViewEntityObjectSetter
     */
    public function getEntityObjectSetter()
    {
        if (!$this->entityObjectSetter) {
            $this->entityObjectSetter = new ProductPageViewEntityObjectSetter();
        }
        return parent::getEntityObjectSetter();
    }
}