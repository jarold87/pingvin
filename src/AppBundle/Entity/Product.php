<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 * @ORM\HasLifecycleCallbacks()
 */
class Product extends Shop
{
    /**
     * @ORM\Id
     * @ORM\Column(name="product_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $productId = null;

    /**
     * @ORM\Column(name="outer_id", type="string", length=100)
     */
    protected $outerId = null;

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
     * @ORM\Column(name="category_outer_id", type="string", length=255)
     */
    protected $categoryOuterId = null;

    /**
     * @ORM\Column(name="is_description", type="integer", length=11)
     */
    protected $isDescription = null;

    /**
     * @ORM\Column(name="status", type="integer", length=11)
     */
    protected $status = null;

    /**
     * @ORM\Column(name="available_date", type="datetime")
     */
    protected $availableDate = null;

    /**
     * @ORM\Column(name="product_create_date", type="datetime")
     */
    protected $productCreateDate = null;

    /**
     * @ORM\OneToMany(targetEntity="ProductStatistics", mappedBy="product")
     */
    protected $productStatistics;

    /**
     * @ORM\OneToMany(targetEntity="OrderProduct", mappedBy="product")
     */
    protected $productOrders;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productStatistics = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set categoryOuterId
     *
     * @param string $categoryOuterId
     *
     * @return Product
     */
    public function setCategoryOuterId($categoryOuterId)
    {
        $this->categoryOuterId = $categoryOuterId;

        return $this;
    }

    /**
     * Get categoryOuterId
     *
     * @return string
     */
    public function getCategoryOuterId()
    {
        return $this->categoryOuterId;
    }

    /**
     * Set isDescription
     *
     * @param integer $isDescription
     *
     * @return Product
     */
    public function setIsDescription($isDescription)
    {
        $this->isDescription = $isDescription;

        return $this;
    }

    /**
     * Get isDescription
     *
     * @return integer
     */
    public function getIsDescription()
    {
        return $this->isDescription;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Product
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
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
     * Add productStatistic
     *
     * @param \AppBundle\Entity\ProductStatistics $productStatistic
     *
     * @return Product
     */
    public function addProductStatistic(\AppBundle\Entity\ProductStatistics $productStatistic)
    {
        $this->productStatistics[] = $productStatistic;

        return $this;
    }

    /**
     * Remove productStatistic
     *
     * @param \AppBundle\Entity\ProductStatistics $productStatistic
     */
    public function removeProductStatistic(\AppBundle\Entity\ProductStatistics $productStatistic)
    {
        $this->productStatistics->removeElement($productStatistic);
    }

    /**
     * Get productStatistics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductStatistics()
    {
        return $this->productStatistics;
    }

    /**
     * Add productOrder
     *
     * @param \AppBundle\Entity\OrderProduct $productOrder
     *
     * @return Product
     */
    public function addProductOrder(\AppBundle\Entity\OrderProduct $productOrder)
    {
        $this->productOrders[] = $productOrder;

        return $this;
    }

    /**
     * Remove productOrder
     *
     * @param \AppBundle\Entity\OrderProduct $productOrder
     */
    public function removeProductOrder(\AppBundle\Entity\OrderProduct $productOrder)
    {
        $this->productOrders->removeElement($productOrder);
    }

    /**
     * Get productOrders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductOrders()
    {
        return $this->productOrders;
    }
}
