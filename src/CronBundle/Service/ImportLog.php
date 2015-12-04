<?php

namespace CronBundle\Service;

use AppBundle\Entity\UserImportLog;
use AppBundle\Entity\GlobalImportLog;

class ImportLog
{
    /** @var float */
    protected $runtime = 0.00;

    /** @var float */
    protected $userRuntime = 0.00;

    /** @var */
    protected $userStartTime;

    /** @var int */
    protected $userLastIndex = 0;

    /** @var int */
    protected $userProcessItemCount = 0;

    /** @var int */
    protected $userUnProcessItemCount = 0;

    /** @var int */
    protected $userShopRequestCount = 0;

    /** @var int */
    protected $allProcessItemCount = 0;

    /** @var string */
    protected $messages = '';

    /** @var UserImportLog */
    protected $userLog;

    /** @var GlobalImportLog */
    protected $globalLog;

    /** @var string */
    protected $error = '';

    /** @var array */
    protected $notAllowed = array();

    /** @var array */
    protected $emptyResponse = array();

    public function resetUserLogData()
    {
        $this->userLog = null;
        $this->userRuntime = 0.00;
        $this->userStartTime = microtime(true);
        $this->userLastIndex = 0;
        $this->userProcessItemCount = 0;
        $this->userUnProcessItemCount = 0;
        $this->userShopRequestCount = 0;
    }

    /**
     * @param $value
     */
    public function setRuntime($value)
    {
        $this->runtime = $value;
    }

    /**
     * @param $value
     */
    public function setUserLastIndex($value)
    {
        $this->userLastIndex = $value;
    }

    /**
     * @param $value
     */
    public function addProcessItemCount($value = 1)
    {
        $this->userProcessItemCount += $value;
        $this->allProcessItemCount += $value;
    }

    /**
     * @param $value
     */
    public function setUnProcessItemCount($value)
    {
        $this->userUnProcessItemCount = $value;
    }

    /**
     * @param $message
     */
    public function addMessage($message)
    {
        $this->messages .= $this->runtime . ' s | ' . round(memory_get_usage() / 1048576.2, 2) . ' MB : ' . $message . "\n";
    }

    /**
     * @param int $value
     */
    public function addShopRequestCount($value = 1)
    {
        $this->userShopRequestCount += $value;
    }

    /**
     * @param $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @param $importName
     * @param $itemValue
     */
    public function addNotAllowed($importName, $itemValue)
    {
        $this->notAllowed[$importName][] = $itemValue;
    }

    /**
     * @param $importName
     * @param $itemValue
     */
    public function addEmptyResponse($importName, $itemValue)
    {
        $this->emptyResponse[$importName][] = $itemValue;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->messages;
    }

    /**
     * @return int
     */
    public function getAllProcessItemCount()
    {
        return $this->allProcessItemCount;
    }

    /**
     * @return UserImportLog
     */
    public function getUserLog()
    {
        if (!$this->userLog) {
            $this->userLog = new UserImportLog();
        }
        $this->refreshUserRuntime();
        $this->userLog->setRunTime($this->userRuntime);
        $this->userLog->setProcessed($this->userProcessItemCount);
        $this->userLog->setUnprocessed($this->userUnProcessItemCount);
        $this->userLog->setShopRequest($this->userShopRequestCount);
        $error = '';
        if ($this->error) {
            $error = $this->error;
        }
        $this->userLog->setError($error);
        $warning = '';
        if ($this->emptyResponse) {
            $warning .= '[Empty Response]';
            foreach ($this->emptyResponse as $key => $values) {
                $valueString = join(',', $values);
                if (strlen($valueString) > 200) {
                    $valueString = substr($valueString, 0, 200) . '...';
                }
                $warning .= ' |' . $key . '| (' . count($values) . ') => ' . $valueString;
            }
        }
        if ($this->notAllowed) {
            $warning .= '[Not Allowed]';
            foreach ($this->notAllowed as $key => $values) {
                $valueString = join(',', $values);
                if (strlen($valueString) > 200) {
                    $valueString = substr($valueString, 0, 200) . '...';
                }
                $warning .= ' |' . $key . '| (' . count($values) . ') => ' . $valueString;
            }
        }
        $this->userLog->setWarning($warning);
        return $this->userLog;
    }

    /**
     * @return GlobalImportLog
     */
    public function getGlobalLog()
    {
        if (!$this->globalLog) {
            $this->globalLog = new GlobalImportLog();
        }
        $this->globalLog->setRunTime($this->runtime);
        $this->globalLog->setProcessed($this->allProcessItemCount);
        $this->globalLog->setMessages($this->messages);
        return $this->globalLog;
    }

    protected function refreshUserRuntime()
    {
        $this->userRuntime = round(microtime(true) - $this->userStartTime, 2);
    }
}