<?php

namespace ShoprenterBundle\Import\Component\ResponseDataConverter;

use CronBundle\Import\Component\ResponseDataConverter;


class ProductCalculateMetricsResponseDataConverter extends ResponseDataConverter
{
    /**
     * @return array
     */
    public function getConvertedData()
    {
        $data = $this->responseData;
        $this->setConvertedData(
            array(

            )
        );
        return parent::getConvertedData();
    }
}