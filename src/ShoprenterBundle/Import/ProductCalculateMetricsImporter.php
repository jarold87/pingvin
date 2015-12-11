<?php
namespace ShoprenterBundle\Import;

use CronBundle\Import\ProductCalculateMetricsImporter as MainProductCalculateMetricsImporter;
use CronBundle\Import\ImporterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductStatistics;
use AppBundle\Entity\OrderProduct;

class ProductCalculateMetricsImporter extends MainProductCalculateMetricsImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'productId';
}