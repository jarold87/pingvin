<?php

namespace CronBundle\Import;

use AppBundle\Entity\Product;

class ProductImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'product';

    /** @var string */
    protected $entity = 'Product';

    /**
     * @param $data
     */
    protected function setProduct($data)
    {
        $this->validateOuterIdInData($data);
        $outerId = $data['outerId'];
        $object = $this->getEntityObject($outerId);
        $object = $this->setDataToObject($object, $data);
        $this->entityManager->persist($object);
    }

    /**
     * @param Product $object
     * @param $data
     * @return Product
     * @throws \Exception
     */
    protected function setDataToObject(Product $object, $data)
    {
        $object->setSku($this->getFormattedData($data, 'sku', 'string'));
        $object->setName($this->getFormattedData($data, 'name', 'string'));
        $object->setPicture($this->getFormattedData($data, 'picture', 'string'));
        $object->setUrl($this->getFormattedData($data, 'url', 'string'));
        $object->setManufacturer($this->getFormattedData($data, 'manufacturer', 'string'));
        $object->setCategory($this->getFormattedData($data, 'category', 'string'));
        $object->setProductCreateDate($this->getFormattedData($data, 'productCreateDate', 'date'));
        return $object;
    }
}