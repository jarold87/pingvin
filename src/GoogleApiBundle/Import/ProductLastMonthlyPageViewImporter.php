<?php

namespace GoogleApiBundle\Import;

use CronBundle\Import\ImporterInterface;

class ProductLastMonthlyPageViewImporter extends ProductPageViewImporter implements ImporterInterface
{
    /** @var string */
    protected $timeKey = 'lastMonthly';

    public function import()
    {
        $this->init();
        $this->requestModel->setLastMonthlyDateInterval();
        parent::import();
    }

    protected function init()
    {
        parent::init();
    }
}