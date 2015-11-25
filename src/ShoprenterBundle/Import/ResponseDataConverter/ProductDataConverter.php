<?php

namespace ShoprenterBundle\Import\ResponseDataConverter;


class ProductDataConverter extends DataConverter
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
            'availableDate' => $data['date_available'],
            'productCreateDate' => $data['date_added'],
        );
        return $this->convertedData;
    }
}