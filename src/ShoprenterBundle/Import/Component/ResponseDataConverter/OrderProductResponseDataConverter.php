<?php

namespace ShoprenterBundle\Import\Component\ResponseDataConverter;

use CronBundle\Import\Component\ResponseDataConverter;


class OrderProductResponseDataConverter extends ResponseDataConverter
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