<?php

namespace CronBundle\Import;

use Doctrine\ORM\EntityManager;
use CronBundle\Service\ImportLog;

abstract class Importer
{
    /** @var string */
    protected $importName;

    /** @var EntityManager */
    protected $entityManager;

    /** @var ClientAdapter */
    protected $client;

    /** @var float */
    protected $actualTime = 0.00;

    /** @var */
    protected $startTime;

    /** @var */
    protected $timeLimit;

    /** @var ImportLog */
    protected $importLog;

    /** @var int */
    protected $timeOut = 0;

    /** @var array */
    protected $error = array();

    /**
     * @param $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @param $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @param $actualTime
     */
    public function setActualTime($actualTime)
    {
        $this->actualTime = $actualTime;
    }

    /**
     * @param $timeLimit
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
    }

    /**
     * @param ImportLog $service
     */
    public function setImportLog(ImportLog $service)
    {
        $this->importLog = $service;
    }

    /**
     * @param array $error
     */
    public function setError(array $error)
    {
        $this->error = $error;
    }

    public function import()
    {

    }

    /**
     * @return array|bool
     */
    public function getError()
    {
        if ($this->error) {
            return $this->error;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isFinishedImport()
    {
        if ($this->timeOut == 1) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function isInLimits()
    {
        if ($this->timeOut == 1) {
            return false;
        }
        $this->refreshActualTime();
        if ($this->actualTime >= $this->timeLimit) {
            return false;
        }
        return true;
    }

    protected function refreshActualTime()
    {
        $this->actualTime = round(microtime(true) - $this->startTime, 2);
    }

    protected function refreshImportLog()
    {
        $this->refreshActualTime();
        $this->importLog->setRuntime($this->actualTime);
    }

    protected function createImportLog()
    {
        $log = $this->importLog->getUserLog();
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}