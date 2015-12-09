<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="import_row_process")
 */
class ImportRowProcess
{
    /**
     * @ORM\Id
     * @ORM\Column(name="row_index", type="integer", length=11)
     */
    protected $rowIndex = null;

    /**
     * @ORM\Column(name="row_key", type="string", length=255)
     */
    protected $rowKey = null;

    /**
     * @ORM\Column(name="row_values", type="text")
     */
    protected $rowValues = null;

    /**
     * Set rowIndex
     *
     * @param integer $rowIndex
     *
     * @return ImportRowProcess
     */
    public function setRowIndex($rowIndex)
    {
        $this->rowIndex = $rowIndex;

        return $this;
    }

    /**
     * Get rowIndex
     *
     * @return integer
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * Set rowKey
     *
     * @param string $rowKey
     *
     * @return ImportRowProcess
     */
    public function setRowKey($rowKey)
    {
        $this->rowKey = $rowKey;

        return $this;
    }

    /**
     * Get rowKey
     *
     * @return string
     */
    public function getRowKey()
    {
        return $this->rowKey;
    }

    /**
     * Set rowValues
     *
     * @param string $rowValues
     *
     * @return ImportRowProcess
     */
    public function setRowValues($rowValues)
    {
        $this->rowValues = $rowValues;

        return $this;
    }

    /**
     * Get rowValues
     *
     * @return string
     */
    public function getRowValues()
    {
        return $this->rowValues;
    }
}
