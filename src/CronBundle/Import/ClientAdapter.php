<?php

namespace CronBundle\Import;

use AppBundle\Service\Setting;
use CronBundle\Service\ImportLog;


abstract class ClientAdapter
{
    /** @var Setting */
    protected $settingService;

    /** @var ImportLog */
    protected $importLog;

    /**
     * @param Setting $settingService
     */
    public function __construct(Setting $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * @param ImportLog $service
     */
    public function setImportLog(ImportLog $service)
    {
        $this->importLog = $service;
    }

    protected function addRequestCount($value = 1)
    {
        if ($this->importLog) {
            $this->importLog->addShopRequestCount($value);
        }
    }
}