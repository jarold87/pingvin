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
        if (isset($this->existEntityKeyByOuterId[$data['outerId']])) {
            /** @var Order $order */
            $order = $this->existEntityCollection->get(
                $this->existEntityKeyByOuterId[$data['outerId']]
            );
            $order->setCustomerOuterId((isset($data['customerOuterId'])) ? $data['customerOuterId'] : '');
            $order->setShippingMethod((isset($data['shippingMethod'])) ? $data['shippingMethod'] : '');
            $order->setPaymentMethod((isset($data['paymentMethod'])) ? $data['paymentMethod'] : '');
            $order->setCurrency((isset($data['currency'])) ? $data['currency'] : '');
            $order->setOrderDate((isset($data['orderDate'])) ? new \DateTime($data['orderDate']) : new \DateTime());
            return;
        }
        $order = new Order();
        $order->setOuterId($data['outerId']);
        $order->setCustomerOuterId((isset($data['customerOuterId'])) ? $data['customerOuterId'] : 0);
        $order->setShippingMethod((isset($data['shippingMethod'])) ? $data['shippingMethod'] : '');
        $order->setPaymentMethod((isset($data['paymentMethod'])) ? $data['paymentMethod'] : '');
        $order->setCurrency((isset($data['currency'])) ? $data['currency'] : '');
        $order->setOrderDate((isset($data['orderDate'])) ? new \DateTime($data['orderDate']) : new \DateTime());
        $this->entityManager->persist($order);
    }
}