<?php

namespace CronBundle\Import\Component\ClientAdapter;

use AppBundle\Service\Setting;
use CronBundle\Service\ImportLog;


abstract class ClientAdapter
{
    /** @var Setting */
    protected $settingService;

    /** @var ImportLog */
    protected $importLog;

    /** @var */
    protected $response;

    /** @var string */
    protected $error = '';

    /**
     * @param Setting $settingService
     */
    public function setSettingService(Setting $settingService)
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

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    public function init()
    {

    }

    /**
     * @param $request
     * @return mixed
     */
    public function getCollectionRequest($request)
    {
        return $this->response;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getRequest($request)
    {
        return $this->response;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getPackageRequest($request)
    {
        return $this->response;
    }

    /**
     * @param int $value
     */
    protected function addRequestCount($value = 1)
    {
        if ($this->importLog) {
            $this->importLog->addShopRequestCount($value);
        }
    }
}