<?php

namespace ShoprenterBundle\Import\Component\EntityObjectSetter;

use CronBundle\Import\Component\ShopEntityObjectSetter;
use AppBundle\Entity\Product;


class ProductEntityObjectSetter extends ShopEntityObjectSetter
{
    /** @var Product */
    protected $object;

    /**
     * @return Product
     */
    public function setDataToObject()
    {
        $this->object->setSku($this->getFormattedData('sku', 'string'));
        $this->object->setName($this->getFormattedData('name', 'string'));
        $this->object->setPicture($this->getFormattedData('picture', 'string'));
        $this->object->setUrl($this->getFormattedData('url', 'string'));
        $this->object->setManufacturer($this->getFormattedData('manufacturer', 'string'));
        $this->object->setCategory($this->getFormattedData('category', 'string'));
        $this->object->setCategoryOuterId($this->getFormattedData('categoryOuterId', 'string'));
        $this->object->setIsDescription($this->getFormattedData('isDescription', 'integer'));
        $this->object->setStatus($this->getFormattedData('status', 'integer'));
        $this->object->setAvailableDate($this->getFormattedData('availableDate', 'date'));
        $this->object->setProductCreateDate($this->getFormattedData('productCreateDate', 'date'));
        parent::setDataToObject();
    }
}