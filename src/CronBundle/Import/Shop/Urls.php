<?php

namespace CronBundle\Import\Shop;

use CronBundle\Import\Shop\ImportInterface;
use CronBundle\Import\Shop\ClientAdapter;

class Urls implements ImportInterface
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $entityManager;

    /** @var ClientAdapter */
    protected $client;

    /** @var */
    protected $actualTime;

    /** @var */
    protected $startTime;

    /** @var */
    protected $timeLimit;

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

    public function isFinishedImport()
    {
        return true;
    }
}