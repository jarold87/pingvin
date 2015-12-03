<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
class Shop
{
    /**
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate = null;

    /**
     * @ORM\Column(name="update_date", type="datetime")
     */
    protected $updateDate = null;

    /**
     * @ORM\Column(name="is_dead", type="integer", length=11)
     */
    protected $isDead = null;


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
     * Set isDead
     *
     * @param integer $isDead
     *
     * @return Product
     */
    public function setIsDead($isDead)
    {
        $this->isDead = $isDead;

        return $this;
    }

    /**
     * Get isDead
     *
     * @return integer
     */
    public function getIsDead()
    {
        return $this->isDead;
    }
}
