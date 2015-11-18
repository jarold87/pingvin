<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="import_log")
 * @ORM\HasLifecycleCallbacks()
 */
class ImportLog
{
    /**
     * @ORM\Id
     * @ORM\Column(name="log_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $logId = null;

    /**
     * @ORM\Column(name="import_name", type="string", length=50)
     */
    protected $importName = null;

    /**
     * @ORM\Column(name="runtime", type="float", scale=2)
     */
    protected $runTime = null;

    /**
     * @ORM\Column(name="processed", type="integer", length=11)
     */
    protected $processed = null;

    /**
     * @ORM\Column(name="unprocessed", type="integer", length=11)
     */
    protected $unprocessed = null;

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
     * Set importName
     *
     * @param string $importName
     *
     * @return ImportLog
     */
    public function setImportName($importName)
    {
        $this->importName = $importName;

        return $this;
    }

    /**
     * Get importName
     *
     * @return string
     */
    public function getImportName()
    {
        return $this->importName;
    }

    /**
     * Set runTime
     *
     * @param float $runTime
     *
     * @return ImportLog
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
     * @return ImportLog
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
     * Set unprocessed
     *
     * @param integer $unprocessed
     *
     * @return ImportLog
     */
    public function setUnprocessed($unprocessed)
    {
        $this->unprocessed = $unprocessed;

        return $this;
    }

    /**
     * Get unprocessed
     *
     * @return integer
     */
    public function getUnprocessed()
    {
        return $this->unprocessed;
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
}
