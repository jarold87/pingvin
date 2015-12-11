<?php
namespace ShoprenterBundle\Import\Component\ItemListCollector;

use CronBundle\Import\Component\ItemListCollector\ItemListCollectorByLoadFromUserDatabase;

class ProductCalculateMetricsItemListCollector extends ItemListCollectorByLoadFromUserDatabase
{
    /** @var string */
    protected $processEntityName = 'ImportItemProcess';
}