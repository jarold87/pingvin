<?php

namespace ShoprenterBundle\Import\EntityObjectSetter;

use CronBundle\Import\ShopEntityObjectSetter;
use AppBundle\Entity\OrderProduct;
use Doctrine\Common\Collections\ArrayCollection;


class OrderProductEntityObjectSetter extends ShopEntityObjectSetter
{
    /** @var OrderProduct */
    protected $object;

    /** @var ArrayCollection */
    protected $orderEntityCollection;

    /** @var array */
    protected $orderEntityKeyByOuterId = array();

    /**
     * @param ArrayCollection $collection
     */
    public function setOrderEntityCollection(ArrayCollection $collection)
    {
        $this->orderEntityCollection = $collection;
    }

    /**
     * @param array $array
     */
    public function setOrderEntityKeyByOuterId(array $array)
    {
        $this->orderEntityKeyByOuterId = $array;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getObject()
    {
        if (!$this->isExistOrderEntityObject($this->data['orderOuterId'])) {
            throw new \Exception('Missing orderOuterId!');
        }
        $order = $this->getObjectFromOrderEntityCollectionByOuterId($this->data['orderOuterId']);
        $this->object->setOrderOuterId($this->getFormattedData('orderOuterId', 'string'));
        $this->object->setProductOuterId($this->getFormattedData('productOuterId', 'string'));
        $this->object->setQuantity($this->getFormattedData('quantity', 'integer'));
        $this->object->setTotal($this->getFormattedData('total', 'integer'));
        $this->object->setOrderDate($this->getFormattedData('orderDate', 'date'));
        $this->object->setOrder($order);
        return parent::getObject();
    }

    /**
     * @param $outerId
     * @return bool
     */
    protected function isExistOrderEntityObject($outerId)
    {
        if (isset($this->orderEntityKeyByOuterId[$outerId])) {
            return true;
        }
        return false;
    }

    /**
     * @param $outerId
     * @return mixed
     */
    protected function getObjectFromOrderEntityCollectionByOuterId($outerId)
    {
        return $this->orderEntityCollection->get(
            $this->orderEntityKeyByOuterId[$outerId]
        );
    }
}