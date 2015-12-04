<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="import_schedule_log")
 * @ORM\HasLifecycleCallbacks()
 */
class ImportScheduleLog
{
    /**
     * @ORM\Id
     * @ORM\Column(name="schedule_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $scheduleId = null;

    /**
     * @ORM\Column(name="user_id", type="integer", length=11)
     */
    protected $userId = null;

    /**
     * @ORM\Column(name="actual_import_index", type="integer", length=11)
     */
    protected $actualImportIndex = null;

    /**
     * @ORM\Column(name="failed_counter", type="integer", length=11)
     */
    protected $failedCounter = null;

    /**
     * @ORM\Column(name="priority", type="integer", length=11)
     */
    protected $priority = null;

    /**
     * @ORM\Column(name="last_finished_import_date", type="datetime")
     */
    protected $lastFinishedImportDate = null;

    /**
     * @ORM\Column(name="handler", type="string", length=10)
     */
    protected $handler = null;

    /**
     * @ORM\Column(name="is_lock", type="integer", length=11)
     */
    protected $isLock = null;

    /**
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate = null;

    /**
     * @ORM\Column(name="update_date", type="datetime")
     */
    protected $updateDate = null;

    /**
     * Get scheduleId
     *
     * @return integer
     */
    public function getScheduleId()
    {
        return $this->scheduleId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return ImportScheduleLog
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set actualImportIndex
     *
     * @param integer $actualImportIndex
     *
     * @return ImportScheduleLog
     */
    public function setActualImportIndex($actualImportIndex)
    {
        $this->actualImportIndex = $actualImportIndex;

        return $this;
    }

    /**
     * Get actualImportIndex
     *
     * @return integer
     */
    public function getActualImportIndex()
    {
        return $this->actualImportIndex;
    }

    /**
     * Set failedCounter
     *
     * @param integer $failedCounter
     *
     * @return ImportScheduleLog
     */
    public function setFailedCounter($failedCounter)
    {
        $this->failedCounter = $failedCounter;

        return $this;
    }

    /**
     * Get failedCounter
     *
     * @return integer
     */
    public function getFailedCounter()
    {
        return $this->failedCounter;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return ImportScheduleLog
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set lastFinishedImportDate
     *
     * @param \DateTime $lastFinishedImportDate
     *
     * @return ImportScheduleLog
     */
    public function setLastFinishedImportDate($lastFinishedImportDate)
    {
        $this->lastFinishedImportDate = $lastFinishedImportDate;

        return $this;
    }

    /**
     * Get lastFinishedImportDate
     *
     * @return \DateTime
     */
    public function getLastFinishedImportDate()
    {
        return $this->lastFinishedImportDate;
    }

    /**
     * Set handler
     *
     * @param \DateTime $handler
     *
     * @return ImportScheduleLog
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Get handler
     *
     * @return \DateTime
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setCreateDate()
    {
        if (!$this->createDate) {
            $this->createDate = new \DateTime();
        }
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdateDate()
    {
        $this->updateDate = new \DateTime();
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set isLock
     *
     * @param integer $isLock
     *
     * @return ImportScheduleLog
     */
    public function setIsLock($isLock)
    {
        $this->isLock = $isLock;

        return $this;
    }

    /**
     * Get isLock
     *
     * @return integer
     */
    public function getIsLock()
    {
        return $this->isLock;
    }
}
