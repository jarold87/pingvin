<?php

namespace ShoprenterBundle\Import\Component\ResponseDataConverter;

use CronBundle\Import\Component\ResponseDataConverter;


class OrderResponseDataConverter extends ResponseDataConverter
{
    /**
     * @return array
     */
    public function getConvertedData()
    {
        $data = $this->responseData;
        $this->setConvertedData(
            array(
                'customerOuterId' => $data['customer_id'],
                'shippingMethod' => $data['shipping_method'],
                'paymentMethod' => $data['payment_method'],
                'currency' => $data['currency'],
                'orderDate' => $data['date_added'],
            )
        );
        return parent::getConvertedData();
    }
}