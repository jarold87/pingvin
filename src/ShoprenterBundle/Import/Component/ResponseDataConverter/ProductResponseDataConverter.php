<?php

namespace ShoprenterBundle\Import\Component\ResponseDataConverter;

use CronBundle\Import\Component\ResponseDataConverter;


class ProductResponseDataConverter extends ResponseDataConverter
{
    /**
     * @return array
     */
    public function getConvertedData()
    {
        $data = $this->responseData;
        $this->setConvertedData(
            array(
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
            )
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