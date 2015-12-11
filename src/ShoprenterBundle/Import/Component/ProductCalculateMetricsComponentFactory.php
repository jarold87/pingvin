<?php

namespace ShoprenterBundle\Import\Component;

use CronBundle\Import\Component\ComponentFactory;
use ShoprenterBundle\Import\Component\ItemListCollector\ProductCalculateMetricsItemListCollector as ItemListCollector;
use ShoprenterBundle\Import\Component\ItemCollector\ProductCalculateMetricsItemCollector as ItemCollector;
use ShoprenterBundle\Import\Component\EntityObjectSetter\ProductCalculateMetricsEntityObjectSetter;

class ProductCalculateMetricsComponentFactory extends ComponentFactory
{
    public function getClientAdapter()
    {
        throw new \Exception('Missing ClientAdapter!');
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
     * @throws \Exception
     */
    public function getRequestModel()
    {
        throw new \Exception('Missing RequestModel!');
    }

    /**
     * @throws \Exception
     */
    public function getResponseDataConverter()
    {
        throw new \Exception('Missing ResponseDataConverter!');
    }

    /**
     * @throws \Exception
     */
    public function getAllowanceValidator()
    {
        throw new \Exception('Missing AllowanceValidator!');
    }

    /**
     * @return ProductCalculateMetricsEntityObjectSetter
     */
    public function getEntityObjectSetter()
    {
        if (!$this->entityObjectSetter) {
            $this->entityObjectSetter = new ProductCalculateMetricsEntityObjectSetter();
        }
        return parent::getEntityObjectSetter();
    }
}