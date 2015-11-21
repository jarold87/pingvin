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
        if (isset($this->existEntityKeyByOuterId[$data['outerId']])) {
            /** @var OrderProduct $orderProduct */
            $orderProduct = $this->existEntityCollection->get(
                $this->existEntityKeyByOuterId[$data['outerId']]
            );
            $orderProduct->setOrderOuterId((isset($data['orderOuterId'])) ? $data['orderOuterId'] : '');
            $orderProduct->setProductOuterId((isset($data['productOuterId'])) ? $data['productOuterId'] : '');
            $orderProduct->setQuantity((isset($data['quantity'])) ? $data['quantity'] : 0);
            $orderProduct->setTotal((isset($data['total'])) ? $data['total'] : 0);
            return;
        }
        $orderProduct = new OrderProduct();
        $orderProduct->setOuterId((isset($data['outerId'])) ? $data['outerId'] : '');
        $orderProduct->setOrderOuterId((isset($data['orderOuterId'])) ? $data['orderOuterId'] : '');
        $orderProduct->setProductOuterId((isset($data['productOuterId'])) ? $data['productOuterId'] : '');
        $orderProduct->setQuantity((isset($data['quantity'])) ? $data['quantity'] : 0);
        $orderProduct->setTotal((isset($data['total'])) ? $data['total'] : 0);
        $this->entityManager->persist($orderProduct);
    }
}