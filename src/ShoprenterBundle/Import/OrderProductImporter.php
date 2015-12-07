<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\OrderProductImporter as MainOrderProductImporter;
use CronBundle\Import\ImporterInterface;

class OrderProductImporter extends MainOrderProductImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'order_product_id';
}