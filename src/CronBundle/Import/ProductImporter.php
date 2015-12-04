<?php

namespace CronBundle\Import;

class ProductImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'product';

    /** @var string */
    protected $entityName = 'Product';

    /** @var ClientAdapter */
    protected $client;
}