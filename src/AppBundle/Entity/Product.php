<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\Column(name="manufacturer", type="string", length=255)
     */
    protected $manufacturer = null;

    /**
     * @ORM\Column(name="category", type="string", length=255)
     */
    protected $category = null;

    /**
     * @ORM\Column(name="outer_id", type="string", length=100)
     */
    protected $outerId = null;

    /**
     * @ORM\Column(name="product_create_date", type="datetime")
     */
    protected $productCreateDate = null;

    /**
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate = null;

    /**
     * @ORM\Column(name="update_date", type="datetime")
     */
    protected $updateDate = null;

    /**
     * @ORM\OneToMany(targetEntity="ProductInformation", mappedBy="product")
     */
    protected $information;

    public function __construct()
    {
        $this->information = new ArrayCollection();
    }

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

    /**
     * Set manufacturer
     *
     * @param string $manufacturer
     *
     * @return Product
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return string
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Product
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set productCreateDate
     *
     * @param \DateTime $productCreateDate
     *
     * @return Product
     */
    public function setProductCreateDate($productCreateDate)
    {
        $this->productCreateDate = $productCreateDate;

        return $this;
    }

    /**
     * Get productCreateDate
     *
     * @return \DateTime
     */
    public function getProductCreateDate()
    {
        return $this->productCreateDate;
    }

    /**
     * Add information
     *
     * @param \AppBundle\Entity\ProductInformation $information
     *
     * @return Product
     */
    public function addInformation(\AppBundle\Entity\ProductInformation $information)
    {
        $this->information[] = $information;

        return $this;
    }

    /**
     * Remove information
     *
     * @param \AppBundle\Entity\ProductInformation $information
     */
    public function removeInformation(\AppBundle\Entity\ProductInformation $information)
    {
        $this->information->removeElement($information);
    }

    /**
     * Get information
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInformation()
    {
        return $this->information;
    }
}
