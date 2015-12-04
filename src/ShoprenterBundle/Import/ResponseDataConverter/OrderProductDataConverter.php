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
        $data = $this->responseData;
        $this->setConvertedData(
            array(
                'orderOuterId' => $data['order_id'],
                'productOuterId' => $data['product_id'],
                'quantity' => $data['quantity'],
                'total' => $data['total'],
                'orderDate' => $data['date_added']
            )
        );
        return parent::getConvertedData();
    }
}