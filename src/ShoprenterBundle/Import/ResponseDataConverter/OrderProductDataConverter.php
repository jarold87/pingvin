<?php

namespace ShoprenterBundle\Import\ResponseDataConverter;

use CronBundle\Import\ResponseDataConverter;


class OrderProductDataConverter extends ResponseDataConverter
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
            'outerId' => $data['order_product_id'],
            'orderOuterId' => $data['order_id'],
            'productOuterId' => $data['product_id'],
            'quantity' => $data['quantity'],
            'total' => $data['total'],
        );
        return parent::getConvertedData();
    }
}