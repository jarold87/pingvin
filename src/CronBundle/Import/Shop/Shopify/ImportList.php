<?php

namespace CronBundle\Import\Shop\Shopify;

use CronBundle\Import\Shop\ImportListInterface;
use CronBundle\Import\Shop\Shopify\Products;
use CronBundle\Import\Shop\Shopify\Urls;

class ImportList implements ImportListInterface
{
    /** @var array */
    protected $imports = array(
        1 => 'Products',
        2 => 'Urls',
    );

    /**
     * @return int
     */
    public function getNumberOfImports()
    {
        return count($this->imports);
    }

    /**
     * @param $index
     * @return Products|Urls|void
     */
    public function getImport($index)
    {
        switch ($index) {
            case 1:
                return new Products();
            case 2:
                return new Urls();
            default:
                return;
        }
    }
}