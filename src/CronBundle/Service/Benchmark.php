<?php

namespace CronBundle\Service;

use AppBundle\Entity\ImportLog;

class Benchmark
{
    public $runtime = 0;

    public $lastIndex = 0;

    public $processItemCount = 0;

    public $unProcessItemCount = 0;

    /** @var ImportLog */
    protected $log;

    public function getLog()
    {
        if (!$this->log) {
            $this->log = new ImportLog();
        }
        $this->log->setImportName('');
        $this->log->setRunTime($this->runtime);
        $this->log->setProcessed($this->processItemCount);
        $this->log->setUnprocessed($this->unProcessItemCount);
        return $this->log;
    }
}