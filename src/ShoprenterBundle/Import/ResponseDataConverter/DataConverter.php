<?php

namespace ShoprenterBundle\Import\ResponseDataConverter;


abstract class DataConverter
{
    /** @var array */
    protected $responseData = array();

    /** @var array */
    protected $convertedData = array();

    /**
     * @param $responseData
     */
    public function setResponseData($responseData)
    {
        $this->responseData = $responseData;
        $this->convertedData = array();
    }
}