<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\CustomerImporter as MainCustomerImporter;
use CronBundle\Import\ImporterInterface;

class CustomerImporter extends MainCustomerImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'customer_id';
}