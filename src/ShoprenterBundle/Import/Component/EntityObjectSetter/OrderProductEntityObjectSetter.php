<?php

namespace ShoprenterBundle\Import\Component\EntityObjectSetter;

use CronBundle\Import\Component\ShopEntityObjectSetter;
use AppBundle\Entity\Order;
use AppBundle\Entity\Product;
use AppBundle\Entity\OrderProduct;
use Doctrine\Common\Collections\ArrayCollection;


class OrderProductEntityObjectSetter extends ShopEntityObjectSetter
{
    /** @var OrderProduct */
    protected $object;

    /** @var ArrayCollection */
    protected $orderEntityCollection;

    /** @var ArrayCollection */
    protected $productEntityCollection;

    /** @var array */
    protected $orderEntityKeyByOuterId = array();

    /** @var array */
    protected $productEntityKeyByOuterId = array();

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
     * @param ArrayCollection $collection
     */
    public function setProductEntityCollection(ArrayCollection $collection)
    {
        $this->productEntityCollection = $collection;
    }

    /**
     * @param array $array
     */
    public function setProductEntityKeyByOuterId(array $array)
    {
        $this->productEntityKeyByOuterId = $array;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function setDataToObject()
    {
        if (!$this->isExistOrderEntityObject($this->data['orderOuterId'])) {
            throw new \Exception('Missing orderOuterId!');
        }
        if (!$this->isExistProductEntityObject($this->data['productOuterId'])) {
            throw new \Exception('Missing productOuterId!');
        }
        /** @var Order $order */
        $order = $this->getObjectFromOrderEntityCollectionByOuterId($this->data['orderOuterId']);
        /** @var Product $product */
        $product = $this->getObjectFromProductEntityCollectionByOuterId($this->data['productOuterId']);
        $this->object->setOrderOuterId($this->getFormattedData('orderOuterId', 'string'));
        $this->object->setProductOuterId($this->getFormattedData('productOuterId', 'string'));
        $this->object->setQuantity($this->getFormattedData('quantity', 'integer'));
        $this->object->setTotal($this->getFormattedData('total', 'integer'));
        $this->object->setOrderDate($this->getFormattedData('orderDate', 'date'));
        $this->object->setOrder($order);
        $this->object->setProduct($product);
        $order->addOrderProduct($this->object);
        $product->addProductOrder($this->object);
        $this->entityManager->persist($order);
        $this->entityManager->persist($product);
        parent::setDataToObject();
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
     * @return bool
     */
    protected function isExistProductEntityObject($outerId)
    {
        if (isset($this->productEntityKeyByOuterId[$outerId])) {
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

    /**
     * @param $outerId
     * @return mixed
     */
    protected function getObjectFromProductEntityCollectionByOuterId($outerId)
    {
        return $this->productEntityCollection->get(
            $this->productEntityKeyByOuterId[$outerId]
        );
    }
}