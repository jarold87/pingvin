<?php

namespace CronBundle\Import;

use AppBundle\Entity\OrderProduct;

class OrderProductImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'order_product';

    /** @var string */
    protected $entity = 'OrderProduct';

    /**
     * @param $data
     */
    protected function setOrderProduct($data)
    {
        $this->validateOuterIdInData($data);
        $outerId = $data['outerId'];
        $object = $this->getEntityObject($outerId);
        $object = $this->setDataToObject($object, $data);
        $this->entityManager->persist($object);
    }

    /**
     * @param OrderProduct $object
     * @param $data
     * @return OrderProduct
     * @throws \Exception
     */
    protected function setDataToObject(OrderProduct $object, $data)
    {
        $object->setOrderOuterId($this->getFormattedData($data, 'orderOuterId', 'string'));
        $object->setProductOuterId($this->getFormattedData($data, 'productOuterId', 'string'));
        $object->setQuantity($this->getFormattedData($data, 'quantity', 'integer'));
        $object->setTotal($this->getFormattedData($data, 'total', 'integer'));
        return $object;
    }
}