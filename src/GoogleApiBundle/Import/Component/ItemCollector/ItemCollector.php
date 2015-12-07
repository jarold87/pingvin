<?php

namespace GoogleApiBundle\Import\Component\ItemCollector;

use CronBundle\Import\Component\ItemCollector\ItemCollectorByValuesInGaProcessList;

class ItemCollector extends ItemCollectorByValuesInGaProcessList
{
    /** @var string */
    protected $processEntityName = 'ImportGaRowProcess';
}