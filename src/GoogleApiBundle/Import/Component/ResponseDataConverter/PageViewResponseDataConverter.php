<?php

namespace GoogleApiBundle\Import\Component\ResponseDataConverter;

use CronBundle\Import\Component\ResponseDataConverter;
use AppBundle\Entity\ImportRowProcess;


class PageViewResponseDataConverter extends ResponseDataConverter
{
    /** @var ImportRowProcess */
    protected $responseData;

    /**
     * @return array
     */
    public function getConvertedData()
    {
        $valuesString = $this->responseData->getRowValues();
        if (!$valuesString) {
            $this->setConvertedData(array());
            return parent::getConvertedData();
        }
        $values = unserialize($valuesString);
        if (!$values) {
            $this->setConvertedData(array());
            return parent::getConvertedData();
        }
        $this->setConvertedData(
            array(
                'views' => $values[1],
                'uniqueViews' => $values[2],
            )
        );
        return parent::getConvertedData();
    }
}