<?php

namespace GoogleApiBundle\Import;

use CronBundle\Import\ImporterInterface;
use GoogleApiBundle\Import\Component\RequestModel\PageViewRequestModel;

class ProductLastMonthlyPageViewImporter extends ProductPageViewImporter implements ImporterInterface
{
    /** @var string */
    protected $timeKey = 'lastMonthly';


    /** @var PageViewRequestModel */
    protected $requestModel;

    public function init()
    {
        parent::init();
        $this->requestModel->setLastMonthlyDateInterval();
    }
}