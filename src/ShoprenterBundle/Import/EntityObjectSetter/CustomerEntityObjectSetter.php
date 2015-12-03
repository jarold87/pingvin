<?php

namespace ShoprenterBundle\Import\EntityObjectSetter;

use CronBundle\Import\ShopEntityObjectSetter;
use AppBundle\Entity\Customer;


class CustomerEntityObjectSetter extends ShopEntityObjectSetter
{
    /** @var Customer */
    protected $object;

    /**
     * @return Customer
     */
    public function getObject()
    {
        $this->object->setLastname($this->getFormattedData('lastname', 'string'));
        $this->object->setFirstname($this->getFormattedData('firstname', 'string'));
        $this->object->setEmail($this->getFormattedData('email', 'string'));
        $this->object->setCustomerGroup($this->getFormattedData('customerGroup', 'string'));
        $this->object->setCompany($this->getFormattedData('company', 'string'));
        $this->object->setCity($this->getFormattedData('city', 'string'));
        $this->object->setCountry($this->getFormattedData('country', 'string'));
        $this->object->setRegistrationDate($this->getFormattedData('registrationDate', 'date'));
        return parent::getObject();
    }
}