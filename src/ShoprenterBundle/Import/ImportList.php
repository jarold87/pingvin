<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\ImportList as MainImportList;
use CronBundle\Import\ImportListInterface;

class ImportList extends MainImportList implements ImportListInterface
{
    /** @var array */
    protected $imports = array(
        1 => array('ProductCalculateMetrics', 'shop', 'ShoprenterBundle'),
        /*1 => array('Product', 'shop', 'ShoprenterBundle'),
        2 => array('Customer', 'shop', 'ShoprenterBundle'),
        3 => array('Order', 'shop', 'ShoprenterBundle'),
        4 => array('OrderProduct', 'shop', 'ShoprenterBundle'),
        5 => array('ProductAllTimePageView', 'GA', 'GoogleApiBundle'),
        6 => array('ProductActualMonthlyPageView', 'GA', 'GoogleApiBundle'),
        7 => array('ProductLastMonthlyPageView', 'GA', 'GoogleApiBundle'),*/
    );
}