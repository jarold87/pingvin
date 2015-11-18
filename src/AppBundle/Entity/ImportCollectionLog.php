<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="import_collection_log")
 * @ORM\HasLifecycleCallbacks()
 */
class ImportCollectionLog
{
    /**
     * @ORM\Id
     * @ORM\Column(name="import_collection_log_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $importCollectionLogId = null;

    /**
     * @ORM\Column(name="import_name", type="string", length=50)
     */
    protected $importName = null;

    /**
     * @ORM\Column(name="last_index", type="integer", length=11)
     */
    protected $lastIndex = null;

    /**
     * @ORM\Column(name="start_date", type="datetime")
     */
    protected $startDate = null;

    /**
     * @ORM\Column(name="finish_date", type="datetime")
     */
    protected $finishDate = null;

    /**
     * Get importCollectionLogId
     *
     * @return integer
     */
    public function getImportCollectionLogId()
    {
        return $this->importCollectionLogId;
    }

    /**
     * Set importName
     *
     * @param string $importName
     *
     * @return ImportCollectionLog
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
     * Set lastIndex
     *
     * @param integer $lastIndex
     *
     * @return ImportCollectionLog
     */
    public function setLastIndex($lastIndex)
    {
        $this->lastIndex = $lastIndex;

        return $this;
    }

    /**
     * Get lastIndex
     *
     * @return integer
     */
    public function getLastIndex()
    {
        return $this->lastIndex;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set finishDate
     *
     * @param integer $finishDate
     *
     * @return ImportCollectionLog
     */
    public function setFinishDate($finishDate)
    {
        $this->finishDate = $finishDate;

        return $this;
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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setStartDate()
    {
        if (!$this->startDate) {
            $this->startDate = new \DateTime();
        }
    }
}
