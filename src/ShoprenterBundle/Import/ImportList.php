<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\ImportList as MainImportList;
use CronBundle\Import\ImportListInterface;

class ImportList extends MainImportList implements ImportListInterface
{
    /** @var array */
    protected $imports = array(
        //1 => array('ProductCalculateMetrics', 'ShoprenterBundle'),
        1 => array('Product', 'ShoprenterBundle'),
        2 => array('Customer', 'ShoprenterBundle'),
        3 => array('Order', 'ShoprenterBundle'),
        4 => array('OrderProduct', 'ShoprenterBundle'),
        5 => array('ProductAllTimePageView', 'GoogleApiBundle'),
        6 => array('ProductActualMonthlyPageView', 'GoogleApiBundle'),
        7 => array('ProductLastMonthlyPageView', 'GoogleApiBundle'),
        8 => array('ProductCalculateMetrics', 'ShoprenterBundle'),
    );
}