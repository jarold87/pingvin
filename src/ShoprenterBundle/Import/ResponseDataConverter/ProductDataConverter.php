<?php

namespace ShoprenterBundle\Import\ResponseDataConverter;

use CronBundle\Import\ResponseDataConverter;


class ProductDataConverter extends ResponseDataConverter
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
            'outerId' => $data['product_id'],
            'sku' => $data['sku'],
            'name' => $data['name'],
            'picture' => $data['image'],
            'url' => $data['url'],
            'manufacturer' => $data['manufacturer'],
            'category' => $data['category'],
            'categoryOuterId' => $data['category_id'],
            'isDescription' => $data['is_description'],
            'status' => $this->convertStatus($data['status']),
            'availableDate' => $data['date_available'],
            'productCreateDate' => $data['date_added'],
        );
        return parent::getConvertedData();
    }

    /**
     * @param $status
     * @return int
     */
    protected function convertStatus($status)
    {
        if ($status == 1) {
            return 1;
        }
        return 0;
    }
}