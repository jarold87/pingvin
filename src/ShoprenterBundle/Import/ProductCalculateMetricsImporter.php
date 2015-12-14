<?php
namespace ShoprenterBundle\Import;

use CronBundle\Import\ProductCalculateMetricsImporter as MainProductCalculateMetricsImporter;
use CronBundle\Import\ImporterInterface;

class ProductCalculateMetricsImporter extends MainProductCalculateMetricsImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'productId';
}