<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\ImportListInterface;

class ImportList implements ImportListInterface
{
    /** @var array */
    protected $imports = array(
        1 => 'Product',
        2 => 'Order',
        3 => 'OrderProduct',
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
     * @return OrderImporter|ProductImporter|void
     */
    public function getImport($index)
    {
        switch ($index) {
            case 1:
                return new ProductImporter();
            case 2:
                return new OrderImporter();
            case 3:
                return new OrderProductImporter();
            default:
                return;
        }
    }
}