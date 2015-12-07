<?php

namespace CronBundle\Import;

use Doctrine\ORM\EntityManager;
use AppBundle\Service\Setting;
use CronBundle\Import\Component\ComponentFactory;
use CronBundle\Service\ImportLog;
use CronBundle\Service\RuntimeWatcher;

interface ImporterInterface
{
    public function setImportName($importName);

    public function setSettingService(Setting $setting);

    public function setEntityManager(EntityManager $entityManager);

    public function setComponentFactory(ComponentFactory $factory);

    public function setImportLog(ImportLog $service);

    public function setRuntimeWatcher(RuntimeWatcher $runtimeWatcher);

    public function init();

    public function import();

    public function getError();

    public function isFinishedImport();
}