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
    protected $runtime = 0.00;

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
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ClientAdapter $client
     */
    public function setClient(ClientAdapter $client)
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
     * @param $runtime
     */
    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;
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

    public function import()
    {
        throw new \Exception("Not a valid importer!");
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
     * @param array $error
     */
    protected function setError(array $error)
    {
        $this->error = $error;
    }

    /**
     * @return bool
     */
    protected function isInLimits()
    {
        if ($this->timeOut == 1) {
            return false;
        }
        $this->refreshRunTime();
        if ($this->runtime >= $this->timeLimit) {
            return false;
        }
        return true;
    }

    protected function refreshRunTime()
    {
        $this->runtime = round(microtime(true) - $this->startTime, 2);
    }

    protected function saveImportLog()
    {
        $this->refreshRunTime();
        $this->importLog->setRuntime($this->runtime);
        $log = $this->importLog->getUserLog();
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    /**
     * @param $data
     * @param $key
     * @param $type
     * @return \DateTime|int|string
     * @throws \Exception
     */
    protected function getFormattedData($data, $key, $type)
    {
        if (!isset($data[$key])) {
            throw new \Exception("Not a valid key or not exist in data!");
        }
        switch ($type) {
            case 'string':
                return (isset($data[$key])) ? $data[$key] : '';
            case 'integer':
                return (isset($data[$key])) ? $data[$key] : 0;
            case 'date':
                return (isset($data[$key])) ? new \DateTime($data[$key]) : new \DateTime();
        }
        return '';
    }
}