<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_statistics")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductStatistics
{
    /**
     * @ORM\Id
     * @ORM\Column(name="product_statistics_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $productStatisticsId = null;

    /**
     * @ORM\Column(name="product_id", type="integer", length=11)
     */
    protected $productId = null;

    /**
     * @ORM\Column(name="time_key", type="string", length=10)
     */
    protected $timeKey = null;

    /**
     * @ORM\Column(name="views", type="integer", length=11)
     */
    protected $views = 0;

    /**
     * @ORM\Column(name="unique_views", type="integer", length=11)
     */
    protected $uniqueViews = 0;

    /**
     * @ORM\Column(name="update_date", type="datetime")
     */
    protected $updateDate = null;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="productStatistics")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     */
    protected $product;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdateDate()
    {
        $this->updateDate = new \DateTime();
    }

    /**
     * Get productStatisticsId
     *
     * @return integer
     */
    public function getProductStatisticsId()
    {
        return $this->productStatisticsId;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     *
     * @return ProductStatistics
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
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
     * Set timeKey
     *
     * @param string $timeKey
     *
     * @return ProductStatistics
     */
    public function setTimeKey($timeKey)
    {
        $this->timeKey = $timeKey;

        return $this;
    }

    /**
     * Get timeKey
     *
     * @return string
     */
    public function getTimeKey()
    {
        return $this->timeKey;
    }

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return ProductStatistics
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set uniqueViews
     *
     * @param integer $uniqueViews
     *
     * @return ProductStatistics
     */
    public function setUniqueViews($uniqueViews)
    {
        $this->uniqueViews = $uniqueViews;

        return $this;
    }

    /**
     * Get uniqueViews
     *
     * @return integer
     */
    public function getUniqueViews()
    {
        return $this->uniqueViews;
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
     * Set product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return ProductStatistics
     */
    public function setProduct(\AppBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \AppBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
