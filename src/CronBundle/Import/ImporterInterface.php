<?php

namespace CronBundle\Import;

use Doctrine\ORM\EntityManager;
use CronBundle\Service\ImportLog;

interface ImporterInterface
{
    public function setEntityManager(EntityManager $entityManager);

    public function setClient(ClientAdapter $client);

    public function setStartTime($startTime);

    public function setRuntime($runtime);

    public function setTimeLimit($timeLimit);

    public function setImportLog(ImportLog $service);

    public function import();

    public function getError();

    public function isFinishedImport();
}