<?php

namespace ShoprenterBundle\Import\Component\EntityObjectSetter;

use CronBundle\Import\Component\ShopEntityObjectSetter;
use AppBundle\Entity\Order;


class OrderEntityObjectSetter extends ShopEntityObjectSetter
{
    /** @var Order */
    protected $object;

    /**
     * @return Order
     */
    public function getObject()
    {
        $this->object->setCustomerOuterId($this->getFormattedData('customerOuterId', 'integer'));
        $this->object->setShippingMethod($this->getFormattedData('shippingMethod', 'string'));
        $this->object->setPaymentMethod($this->getFormattedData('paymentMethod', 'string'));
        $this->object->setCurrency($this->getFormattedData('currency', 'string'));
        $this->object->setOrderDate($this->getFormattedData('orderDate', 'date'));
        return parent::getObject();
    }
}