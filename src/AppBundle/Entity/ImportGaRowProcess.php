<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="import_ga_row_process")
 */
class ImportGaRowProcess
{
    /**
     * @ORM\Id
     * @ORM\Column(name="row_index", type="integer", length=11)
     */
    protected $rowIndex = null;

    /**
     * @ORM\Column(name="dimension_key", type="string", length=255)
     */
    protected $dimensionKey = null;

    /**
     * @ORM\Column(name="row_values", type="text")
     */
    protected $rowValues = null;

    /**
     * Set rowIndex
     *
     * @param integer $rowIndex
     *
     * @return ImportGaRowProcess
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
     * Set dimensionKey
     *
     * @param string $dimensionKey
     *
     * @return ImportGaRowProcess
     */
    public function setDimensionKey($dimensionKey)
    {
        $this->dimensionKey = $dimensionKey;

        return $this;
    }

    /**
     * Get dimensionKey
     *
     * @return string
     */
    public function getDimensionKey()
    {
        return $this->dimensionKey;
    }

    /**
     * Set rowValues
     *
     * @param string $rowValues
     *
     * @return ImportGaRowProcess
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
