<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 * @ORM\HasLifecycleCallbacks()
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(name="product_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $productId = null;

    /**
     * @ORM\Column(name="sku", type="string", length=255)
     */
    protected $sku = null;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name = null;

    /**
     * @ORM\Column(name="picture", type="string", length=255)
     */
    protected $picture = null;

    /**
     * @ORM\Column(name="url", type="string", length=255)
     */
    protected $url = null;

    /**
     * @ORM\Column(name="available_date", type="date")
     */
    protected $availableDate = null;

    /**
     * @ORM\Column(name="outer_id", type="string", length=100)
     */
    protected $outerId = null;

    /**
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate = null;

    /**
     * @ORM\Column(name="update_date", type="datetime")
     */
    protected $updateDate = null;

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set sku
     *
     * @param string $sku
     *
     * @return Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * Set picture
     *
     * @param string $picture
     *
     * @return Product
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set availableDate
     *
     * @param \DateTime $availableDate
     *
     * @return Product
     */
    public function setAvailableDate($availableDate)
    {
        $this->availableDate = $availableDate;

        return $this;
    }

    /**
     * Get availableDate
     *
     * @return \DateTime
     */
    public function getAvailableDate()
    {
        return $this->availableDate;
    }

    /**
     * Set outerId
     *
     * @param string $outerId
     *
     * @return Product
     */
    public function setOuterId($outerId)
    {
        $this->outerId = $outerId;

        return $this;
    }

    /**
     * Get outerId
     *
     * @return string
     */
    public function getOuterId()
    {
        return $this->outerId;
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
     * Set url
     *
     * @param string $url
     *
     * @return Product
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
