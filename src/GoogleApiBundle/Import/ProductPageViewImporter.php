<?php

namespace GoogleApiBundle\Import;

class ProductPageViewImporter extends AnalyticsImporter
{
    /** @var string GA dimension */
    protected $outerIdKey = 'Url';

    /** @var string */
    protected $entityName = 'Product';
}