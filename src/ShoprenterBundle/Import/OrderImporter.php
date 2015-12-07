<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\OrderImporter as MainOrderImporter;
use CronBundle\Import\ImporterInterface;

class OrderImporter extends MainOrderImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'order_id';
}