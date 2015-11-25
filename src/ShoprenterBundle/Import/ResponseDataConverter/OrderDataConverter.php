<?php

namespace ShoprenterBundle\Import\ResponseDataConverter;

use CronBundle\Import\ResponseDataConverter;


class OrderDataConverter extends ResponseDataConverter
{
    /**
     * @return array
     */
    public function getConvertedData()
    {
        if ($this->convertedData) {
            return $this->convertedData;
        }
        $data = $this->responseData;
        $this->convertedData = array(
            'outerId' => $data['order_id'],
            'customerOuterId' => $data['customer_id'],
            'shippingMethod' => $data['shipping_method'],
            'paymentMethod' => $data['payment_method'],
            'currency' => $data['currency'],
            'orderDate' => $data['date_added'],
        );
        return parent::getConvertedData();
    }
}