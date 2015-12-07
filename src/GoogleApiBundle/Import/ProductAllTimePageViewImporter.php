<?php

namespace GoogleApiBundle\Import;

use CronBundle\Import\ImporterInterface;

class ProductAllTimePageViewImporter extends ProductPageViewImporter implements ImporterInterface
{
    /** @var string */
    protected $timeKey = 'all';
}