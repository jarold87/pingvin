<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="import_item_process")
 */
class ImportItemProcess
{
    /**
     * @ORM\Id
     * @ORM\Column(name="item_index", type="integer", length=11)
     */
    protected $itemIndex = null;

    /**
     * @ORM\Column(name="item_value", type="string", length=255)
     */
    protected $itemValue = null;

    /**
     * Set itemIndex
     *
     * @param integer $itemIndex
     *
     * @return ImportItemProcess
     */
    public function setItemIndex($itemIndex)
    {
        $this->itemIndex = $itemIndex;

        return $this;
    }

    /**
     * Get itemIndex
     *
     * @return integer
     */
    public function getItemIndex()
    {
        return $this->itemIndex;
    }

    /**
     * Set itemValue
     *
     * @param string $itemValue
     *
     * @return ImportItemProcess
     */
    public function setItemValue($itemValue)
    {
        $this->itemValue = $itemValue;

        return $this;
    }

    /**
     * Get itemValue
     *
     * @return string
     */
    public function getItemValue()
    {
        return $this->itemValue;
    }
}
