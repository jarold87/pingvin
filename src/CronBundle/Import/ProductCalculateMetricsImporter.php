<?php

namespace CronBundle\Import;

class ProductCalculateMetricsImporter extends CalculateMetricsImporter
{
    /** @var string */
    protected $entityName = 'ProductStatistics';

    /** @var string */
    protected $sourceEntityName = 'Product';
}