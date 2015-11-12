<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="setting")
 */
class Setting
{
    /**
     * @ORM\Id
     * @ORM\Column(name="setting_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $settingId = null;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name = null;

    /**
     * @ORM\Column(name="value", type="string", length=250)
     */
    protected $value = null;

    /**
     * @ORM\Column(name="update_date", type="datetime")
     */
    protected $updateDate = null;

    /**
     * Get settingId
     *
     * @return integer
     */
    public function getSettingId()
    {
        return $this->settingId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Setting
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Setting
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
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
}
