<?php

namespace CronBundle\Import;

use Doctrine\ORM\EntityManager;

abstract class Importer
{
    /** @var string */
    protected $importName;

    /** @var EntityManager */
    protected $entityManager;

    /** @var ClientAdapter */
    protected $client;

    /** @var */
    protected $actualTime;

    /** @var */
    protected $startTime;

    /** @var */
    protected $timeLimit;

    /** @var int */
    protected $timeOut = 0;

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

    public function import()
    {

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
        $this->actualTime = round(microtime(true) - $this->startTime);
        if ($this->actualTime >= $this->timeLimit) {
            return false;
        }
        return true;
    }
}