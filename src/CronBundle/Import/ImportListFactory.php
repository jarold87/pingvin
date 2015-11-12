<?php

namespace CronBundle\Import;

use CronBundle\Import\Shop\Sr\ImportList as SrImportList;
use CronBundle\Import\Shop\Shopify\ImportList as ShopifyImportList;

class ImportListFactory
{
    /** @var string */
    protected $shop = '';

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $entityManager;

    /** @var string */
    protected $srId = 'SR';

    /** @var string */
    protected $shopifyId = 'Shopify';

    /**
     * @param $shop
     */
    public function __construct($shop)
    {
        $this->shop = $shop;
    }

    /**
     * @return ShopifyImportList|SrImportList|void
     */
    public function getImportList()
    {
        switch ($this->shop) {
            case $this->srId:
                return new SrImportList();
            case $this->shopifyId:
                return new ShopifyImportList();
            default:
                return;
        }
    }
}