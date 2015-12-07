<?php

namespace CronBundle\Service;

class RuntimeWatcher
{
    /** @var float */
    protected $runtime = 0.00;

    /** @var */
    protected $startTime;

    /** @var int */
    protected $timeLimit;

    /** @var int */
    protected $timeOut = 0;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * @param $timeLimit
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
    }

    /**
     * @return float
     */
    public function getRuntime()
    {
        $this->refreshRunTime();
        return $this->runtime;
    }

    /**
     * @return bool
     */
    public function isInTimeLimit()
    {
        if ($this->timeOut == 1) {
            return false;
        }
        $this->refreshRunTime();
        if ($this->runtime >= $this->timeLimit) {
            $this->timeOut = 1;
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isTimeOut()
    {
        if ($this->timeOut == 1) {
            return true;
        }
        return false;
    }


    protected function refreshRunTime()
    {
        $this->runtime = round(microtime(true) - $this->startTime, 2);
    }
}