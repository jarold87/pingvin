<?php

namespace GoogleApiBundle\Import;

use CronBundle\Import\ImporterInterface;

class ProductActualMonthlyPageViewImporter extends ProductPageViewImporter implements ImporterInterface
{
    /** @var string */
    protected $timeKey = 'actualMonthly';

    public function import()
    {
        $this->init();
        $this->requestModel->setActualMonthlyDateInterval();
        parent::import();
    }

    protected function init()
    {
        parent::init();
    }
}