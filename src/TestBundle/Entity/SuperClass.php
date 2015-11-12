<?php
namespace TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
class SuperClass
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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdateDate()
    {
        $this->updateDate = new \DateTime();
    }
}
