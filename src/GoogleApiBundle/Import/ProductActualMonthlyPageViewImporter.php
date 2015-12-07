<?php

namespace GoogleApiBundle\Import;

use CronBundle\Import\ImporterInterface;
use GoogleApiBundle\Import\Component\RequestModel\PageViewRequestModel;

class ProductActualMonthlyPageViewImporter extends ProductPageViewImporter implements ImporterInterface
{
    /** @var string */
    protected $timeKey = 'actualMonthly';

    /** @var PageViewRequestModel */
    protected $requestModel;

    public function init()
    {
        parent::init();
        $this->requestModel->setActualMonthlyDateInterval();
    }
}