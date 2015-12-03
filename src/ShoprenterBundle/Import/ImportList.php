<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\ImportListInterface;
use GoogleApiBundle\Import\ProductAllTimePageViewImporter;
use GoogleApiBundle\Import\ProductActualMonthlyPageViewImporter;
use GoogleApiBundle\Import\ProductLastMonthlyPageViewImporter;

class ImportList implements ImportListInterface
{
    /** @var array */
    protected $imports = array(
        1 => array('Product', 'shop'),
        2 => array('Customer', 'shop'),
        3 => array('Order', 'shop'),
        4 => array('OrderProduct', 'shop'),
        //5 => array('ProductAllTimePageView', 'GA'),
        //6 => array('ProductActualMonthlyPageView', 'GA'),
        //7 => array('ProductLastMonthlyPageView', 'GA'),
    );

    /** @var int */
    protected $importIndex = 0;

    /**
     * @return int
     */
    public function getNumberOfImports()
    {
        return count($this->imports);
    }

    /**
     * @param $index
     */
    public function setImportIndex($index)
    {
        $this->importIndex = $index;
    }

    /**
     * @return ProductActualMonthlyPageViewImporter|ProductAllTimePageViewImporter|CustomerImporter|OrderImporter|OrderProductImporter|ProductImporter|ProductLastMonthlyPageViewImporter|void
     */
    public function getImport()
    {
        switch ($this->importIndex) {
            case 1:
                return new ProductImporter();
            case 2:
                return new CustomerImporter();
            case 3:
                return new OrderImporter();
            case 4:
                return new OrderProductImporter();
            case 5:
                return new ProductAllTimePageViewImporter();
            case 6:
                return new ProductActualMonthlyPageViewImporter();
            case 7:
                return new ProductLastMonthlyPageViewImporter();
            default:
                return;
        }
    }

    /**
     * @return mixed
     */
    public function getImportSourceType()
    {
        return $this->imports[$this->importIndex][1];
    }
}