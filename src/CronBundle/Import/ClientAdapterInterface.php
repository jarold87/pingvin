<?php

namespace CronBundle\Import;

use CronBundle\Service\ImportLog;

interface ClientAdapterInterface
{
    public function setImportLog(ImportLog $service);

    public function init();

    public function getCollectionRequest($request);

    public function getRequest($request);
}