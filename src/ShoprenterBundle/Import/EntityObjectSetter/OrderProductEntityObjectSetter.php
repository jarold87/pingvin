<?php

namespace ShoprenterBundle\Import\EntityObjectSetter;

use CronBundle\Import\EntityObjectSetter;
use AppBundle\Entity\OrderProduct;


class OrderProductEntityObjectSetter extends EntityObjectSetter
{
    /** @var OrderProduct */
    protected $object;

    /**
     * @return OrderProduct
     */
    public function getObject()
    {
        $this->object->setOrderOuterId($this->getFormattedData('orderOuterId', 'string'));
        $this->object->setProductOuterId($this->getFormattedData('productOuterId', 'string'));
        $this->object->setQuantity($this->getFormattedData('quantity', 'integer'));
        $this->object->setTotal($this->getFormattedData('total', 'integer'));
        return parent::getObject();
    }
}