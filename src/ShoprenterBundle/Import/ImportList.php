<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\ImportListInterface;

class ImportList implements ImportListInterface
{
    /** @var array */
    protected $imports = array(
        1 => 'Product',
        2 => 'Customer',
        3 => 'Order',
        4 => 'OrderProduct',
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
     * @return CustomerImporter|OrderImporter|OrderProductImporter|ProductImporter|void
     */
    public function getImport($index)
    {
        switch ($index) {
            case 1:
                return new ProductImporter();
            case 2:
                return new CustomerImporter();
            case 3:
                return new OrderImporter();
            case 4:
                return new OrderProductImporter();
            default:
                return;
        }
    }
}