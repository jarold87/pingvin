<?php

namespace CronBundle\Import;

use AppBundle\Entity\Order;

class OrderImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'order';

    /** @var string */
    protected $entity = 'Order';

    /**
     * @param $data
     */
    protected function setOrder($data)
    {
        $this->validateOuterIdInData($data);
        $outerId = $data['outerId'];
        $object = $this->getEntityObject($outerId);
        $object = $this->setDataToObject($object, $data);
        $this->entityManager->persist($object);
    }

    /**
     * @param Order $object
     * @param $data
     * @return Order
     * @throws \Exception
     */
    protected function setDataToObject(Order $object, $data)
    {
        $object->setCustomerOuterId($this->getFormattedData($data, 'customerOuterId', 'integer'));
        $object->setShippingMethod($this->getFormattedData($data, 'shippingMethod', 'string'));
        $object->setPaymentMethod($this->getFormattedData($data, 'paymentMethod', 'string'));
        $object->setCurrency($this->getFormattedData($data, 'currency', 'string'));
        $object->setOrderDate($this->getFormattedData($data, 'orderDate', 'date'));
        return $object;
    }
}