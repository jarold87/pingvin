<?php

namespace GoogleApiBundle\Import\Component\ItemListCollector;

use CronBundle\Import\Component\ItemListCollector\ItemListCollectorByOneRequest;

class ItemListCollector extends ItemListCollectorByOneRequest
{
    /** @var string */
    protected $processEntityName = 'ImportGaRowProcess';
}