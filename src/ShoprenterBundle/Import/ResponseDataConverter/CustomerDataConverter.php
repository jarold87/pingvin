<?php

namespace ShoprenterBundle\Import\ResponseDataConverter;

use CronBundle\Import\ResponseDataConverter;


class CustomerDataConverter extends ResponseDataConverter
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
            'outerId' => $data['customer_id'],
            'lastname' => $data['lastname'],
            'firstname' => $data['firstname'],
            'email' => $data['email'],
            'registrationDate' => $data['date_added'],
            'customerGroup' => $data['customer_group'],
            'company' => $data['company'],
            'city' => $data['city'],
            'country' => $data['country'],
        );
        return parent::getConvertedData();
    }
}