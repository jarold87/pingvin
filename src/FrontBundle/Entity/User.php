<?php
namespace FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="FrontBundle\Repository\User")
 * @ORM\Table(name="user")
 */
class User extends SuperClass
{
    /**
     * @ORM\Id
     * @ORM\Column(name="user_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $userId = null;

    /**
     * @ORM\Column(name="username", type="string", length=10)
     */
    protected $username = null;

    /**
     * @ORM\Column(name="password", type="string", length=50)
     */
    protected $password = null;

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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return User
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }
}
