<?php

namespace CronBundle\Import;

use Doctrine\ORM\EntityManager;
use CronBundle\Service\ImportLog;
use AppBundle\Entity\ImportCollectionLog;
use AppBundle\Entity\ImportItemLog;

abstract class Importer
{
    /** @var int 1000 */
    protected $flushItemPackageNumber = 1000;

    /** @var int 10000*/
    protected $itemProcessLimit = 10000;

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

    /** @var ImportCollectionLog */
    protected $collectionLog;

    /** @var ImportItemLog */
    protected $itemLog;

    /** @var int */
    protected $counterToFlush = 0;

    /** @var array */
    private $error = array();

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
        if ($this->getError()) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function hasInProcessItemRequests()
    {
        $log = $this->getImportItemLog();
        if (!$log) {
            return false;
        }
        $this->itemLog = $log;
        return true;
    }


    /**
     * @return bool
     */
    protected function hasInProcessCollectionRequests()
    {
        $log = $this->entityManager->getRepository('AppBundle:ImportCollectionLog')->findOneBy(
            array('importName' => $this->importName, 'finishDate' => null)
        );
        if (!$log) {
            return false;
        }
        $this->collectionLog = $log;
        return true;
    }

    /**
     * @return null|object
     */
    protected function getImportItemLog()
    {
        $log = $this->entityManager->getRepository('AppBundle:ImportItemLog')->findOneBy(
            array('importName' => $this->importName, 'finishDate' => null)
        );
        return $log;
    }

    /**
     * @param $page
     */
    protected function setCollectionLogIndex($page)
    {
        if ($this->collectionLog) {
            $this->collectionLog->setLastIndex($page);
            return;
        }
        $log = new ImportCollectionLog();
        $log->setImportName($this->importName);
        $log->setLastIndex($page);
        $log->setFinishDate(new \DateTime('0000-00-00'));
        $this->entityManager->persist($log);
        $this->collectionLog = $log;
    }

    /**
     * @param $index
     */
    protected function setItemLogIndex($index)
    {
        if ($this->itemLog) {
            $this->itemLog->setLastIndex($index);
            return;
        }
        $log = new ImportItemLog();
        $log->setImportName($this->importName);
        $log->setLastIndex($index);
        $log->setFinishDate(new \DateTime('0000-00-00'));
        $this->entityManager->persist($log);
        $this->itemLog = $log;
    }

    protected function setItemLogFinish()
    {
        $this->itemLog->setFinishDate(new \DateTime());
    }

    protected function setCollectionLogFinish()
    {
        $this->collectionLog->setFinishDate(new \DateTime());
    }

    /**
     * @return int
     */
    protected function getLastItemIndexFromLog()
    {
        if (!$this->itemLog) {
            return 0;
        }
        return $this->itemLog->getLastIndex();
    }

    /**
     * @return int
     */
    protected function getNextCollectionIndexFromLog()
    {
        return $this->collectionLog->getLastIndex() + 1;
    }

    /**
     * @param $error
     */
    protected function addError($error)
    {
        $this->error[] = $error;
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
        $error = $this->getError();
        $this->importLog->setError($error[0]);
        $log = $this->importLog->getUserLog();
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    protected function manageFlush()
    {
        if ($this->counterToFlush == $this->flushItemPackageNumber) {
            $this->entityManager->flush();
            $this->counterToFlush = 0;
        }
        $this->counterToFlush++;
    }
}