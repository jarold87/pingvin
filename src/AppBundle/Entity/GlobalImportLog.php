<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="import_log")
 * @ORM\HasLifecycleCallbacks()
 */
class GlobalImportLog
{
    /**
     * @ORM\Id
     * @ORM\Column(name="log_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $logId = null;

    /**
     * @ORM\Column(name="runtime", type="float", scale=2)
     */
    protected $runTime = null;

    /**
     * @ORM\Column(name="processed", type="integer", length=11)
     */
    protected $processed = null;

    /**
     * @ORM\Column(name="messages", type="text")
     */
    protected $messages = null;

    /**
     * @ORM\Column(name="start_date", type="datetime")
     */
    protected $startDate = null;

    /**
     * @ORM\Column(name="finish_date", type="datetime")
     */
    protected $finishDate = null;

    /**
     * Get logId
     *
     * @return integer
     */
    public function getLogId()
    {
        return $this->logId;
    }

    /**
     * Set runTime
     *
     * @param float $runTime
     *
     * @return UserImportLog
     */
    public function setRunTime($runTime)
    {
        $this->runTime = $runTime;

        return $this;
    }

    /**
     * Get runTime
     *
     * @return float
     */
    public function getRunTime()
    {
        return $this->runTime;
    }

    /**
     * Set processed
     *
     * @param integer $processed
     *
     * @return UserImportLog
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get processed
     *
     * @return integer
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setStartDate()
    {
        if (!$this->startDate) {
            $this->startDate = new \DateTime();
        }
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setFinishDate()
    {
        $this->finishDate = new \DateTime();
    }

    /**
     * Get finishDate
     *
     * @return \DateTime
     */
    public function getFinishDate()
    {
        return $this->finishDate;
    }

    /**
     * Set messages
     *
     * @param string $messages
     *
     * @return GlobalImportLog
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Get messages
     *
     * @return string
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
