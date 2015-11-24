<?php

namespace CronBundle\Import;

use AppBundle\Entity\Customer;

class CustomerImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'customer';

    /** @var string */
    protected $entity = 'Customer';

    /**
     * @param $data
     */
    protected function setCustomer($data)
    {
        $this->validateOuterIdInData($data);
        $outerId = $data['outerId'];
        $object = $this->getEntityObject($outerId);
        $object = $this->setDataToObject($object, $data);
        $this->entityManager->persist($object);
    }

    /**
     * @param Customer $object
     * @param $data
     * @return Customer
     * @throws \Exception
     */
    protected function setDataToObject(Customer $object, $data)
    {
        $object->setLastname($this->getFormattedData($data, 'lastname', 'string'));
        $object->setFirstname($this->getFormattedData($data, 'firstname', 'string'));
        $object->setEmail($this->getFormattedData($data, 'email', 'string'));
        $object->setCustomerGroup($this->getFormattedData($data, 'objectGroup', 'string'));
        $object->setCompany($this->getFormattedData($data, 'company', 'string'));
        $object->setCity($this->getFormattedData($data, 'city', 'string'));
        $object->setCountry($this->getFormattedData($data, 'country', 'string'));
        $object->setRegistrationDate($this->getFormattedData($data, 'registrationDate', 'date'));
        return $object;
    }
}