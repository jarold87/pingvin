<?php

namespace ShoprenterBundle\Import\Component\ResponseDataConverter;

use CronBundle\Import\Component\ResponseDataConverter;


class CustomerResponseDataConverter extends ResponseDataConverter
{
    /**
     * @return array
     */
    public function getConvertedData()
    {
        $data = $this->responseData;
        $this->setConvertedData(
            array(
                'lastname' => $data['lastname'],
                'firstname' => $data['firstname'],
                'email' => $data['email'],
                'registrationDate' => $data['date_added'],
                'customerGroup' => $data['customer_group'],
                'company' => $data['company'],
                'city' => $data['city'],
                'country' => $data['country'],
            )
        );
        return parent::getConvertedData();
    }
}