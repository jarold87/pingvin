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
        if (isset($this->existEntityKeyByOuterId[$data['outerId']])) {
            /** @var Customer $customer */
            $customer = $this->existEntityCollection->get(
                $this->existEntityKeyByOuterId[$data['outerId']]
            );
            $customer->setLastname((isset($data['lastname'])) ? $data['lastname'] : '');
            $customer->setFirstname((isset($data['firstname'])) ? $data['firstname'] : '');
            $customer->setEmail((isset($data['email'])) ? $data['email'] : '');
            $customer->setCustomerGroup((isset($data['customerGroup'])) ? $data['customerGroup'] : '');
            $customer->setCompany((isset($data['company'])) ? $data['company'] : '');
            $customer->setCity((isset($data['city'])) ? $data['city'] : '');
            $customer->setCountry((isset($data['country'])) ? $data['country'] : '');
            $customer->setRegistrationDate((isset($data['registrationDate'])) ? new \DateTime($data['registrationDate']) : new \DateTime());
            return;
        }
        $customer = new Customer();
        $customer->setOuterId($data['outerId']);
        $customer->setLastname((isset($data['lastname'])) ? $data['lastname'] : '');
        $customer->setFirstname((isset($data['firstname'])) ? $data['firstname'] : '');
        $customer->setEmail((isset($data['email'])) ? $data['email'] : '');
        $customer->setCustomerGroup((isset($data['customerGroup'])) ? $data['customerGroup'] : '');
        $customer->setCompany((isset($data['company'])) ? $data['company'] : '');
        $customer->setCity((isset($data['city'])) ? $data['city'] : '');
        $customer->setCountry((isset($data['country'])) ? $data['country'] : '');
        $customer->setRegistrationDate((isset($data['registrationDate'])) ? new \DateTime($data['registrationDate']) : new \DateTime());
        $this->entityManager->persist($customer);
    }
}