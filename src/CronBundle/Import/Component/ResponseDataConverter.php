<?php

namespace CronBundle\Import\Component;


abstract class ResponseDataConverter
{
    /** @var string */
    protected $outerId = '';

    /** @var array */
    protected $responseData = array();

    /** @var array */
    private $convertedData = array();

    /**
     * @param $id
     */
    public function setOuterId($id)
    {
        $this->outerId = $id;
    }

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
        return $this->convertedData = array_merge(
            array('outerId' => $this->outerId),
            $this->convertedData
        );
    }

    /**
     * @param array $array
     */
    protected function setConvertedData(array $array)
    {
        $this->convertedData = $array;
    }
}