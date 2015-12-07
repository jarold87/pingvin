<?php

namespace ShoprenterBundle\Import\Component\ItemCollector;

use CronBundle\Import\Component\ItemCollector\ItemCollectorByMorePerRequest;

class ItemCollector extends ItemCollectorByMorePerRequest
{
    /** @var string */
    protected $processEntityName = 'ImportItemProcess';
}