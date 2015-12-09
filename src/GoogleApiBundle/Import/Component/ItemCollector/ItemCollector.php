<?php

namespace GoogleApiBundle\Import\Component\ItemCollector;

use CronBundle\Import\Component\ItemCollector\ItemCollectorByValuesInRowProcessList;

class ItemCollector extends ItemCollectorByValuesInRowProcessList
{
    /** @var string */
    protected $processEntityName = 'ImportRowProcess';
}