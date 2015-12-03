<?php

namespace GoogleApiBundle\Import\ResponseDataConverter;

use CronBundle\Import\ResponseDataConverter;
use AppBundle\Entity\ImportGaRowProcess;


class PageViewResponseDataConverter extends ResponseDataConverter
{
    /** @var ImportGaRowProcess */
    protected $responseData;

    /**
     * @return array
     */
    public function getConvertedData()
    {
        if ($this->convertedData) {
            return $this->convertedData;
        }
        $valuesString = $this->responseData->getRowValues();
        if (!$valuesString) {
            $this->convertedData = array();
            return parent::getConvertedData();
        }
        $values = unserialize($valuesString);
        if (!$values) {
            $this->convertedData = array();
            return parent::getConvertedData();
        }
        $this->convertedData = array(
            'views' => $values[1],
            'uniqueViews' => $values[2],
        );
        return parent::getConvertedData();
    }
}