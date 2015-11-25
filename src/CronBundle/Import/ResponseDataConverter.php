<?php

namespace CronBundle\Import;


abstract class ResponseDataConverter
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

    /**
     * @return array
     */
    public function getConvertedData()
    {
        return $this->convertedData;
    }
}