<?php

namespace ShoprenterBundle\Import\EntityObjectSetter;

use CronBundle\Import\EntityObjectSetter;
use AppBundle\Entity\Product;


class ProductEntityObjectSetter extends EntityObjectSetter
{
    /** @var Product */
    protected $object;

    /**
     * @return Product
     */
    public function getObject()
    {
        $this->object->setSku($this->getFormattedData('sku', 'string'));
        $this->object->setName($this->getFormattedData('name', 'string'));
        $this->object->setPicture($this->getFormattedData('picture', 'string'));
        $this->object->setUrl($this->getFormattedData('url', 'string'));
        $this->object->setManufacturer($this->getFormattedData('manufacturer', 'string'));
        $this->object->setCategory($this->getFormattedData('category', 'string'));
        $this->object->setProductCreateDate($this->getFormattedData('productCreateDate', 'date'));
        return parent::getObject();
    }
}