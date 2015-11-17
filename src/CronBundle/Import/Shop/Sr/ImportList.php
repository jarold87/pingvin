<?php

namespace CronBundle\Import\Shop\Sr;

use CronBundle\Import\Shop\ImportListInterface;
use CronBundle\Import\Shop\Sr\Products;
use CronBundle\Import\Shop\Sr\Urls;

class ImportList implements ImportListInterface
{
    /** @var array */
    protected $imports = array(
        1 => 'Products',
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