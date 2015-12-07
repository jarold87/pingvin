<?php

namespace CronBundle\Import\Component\ClientAdapter;

use AppBundle\Service\Setting;
use CronBundle\Service\ImportLog;

interface ClientAdapterInterface
{
    public function setSettingService(Setting $setting);

    public function setImportLog(ImportLog $service);

    public function init();

    public function getCollectionRequest($request);

    public function getRequest($request);
}