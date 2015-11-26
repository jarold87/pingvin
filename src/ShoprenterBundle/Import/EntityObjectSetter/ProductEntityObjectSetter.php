<?php

namespace ShoprenterBundle\Import\EntityObjectSetter;

use CronBundle\Import\EntityObjectSetter;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInformation;


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

        $object = new ProductInformation();
        $object->setInformationKey('teszt1');
        $object->setInformationValue('teszt1');
        $object->setProduct($this->object);
        $this->informationObject[] = $object;

        $object = new ProductInformation();
        $object->setInformationKey('teszt2');
        $object->setInformationValue('teszt2');
        $object->setProduct($this->object);
        $this->informationObject[] = $object;

        return parent::getObject();
    }
}