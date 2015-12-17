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
     * @ORM\Column(name="calculated_views", type="integer", length=11)
     */
    protected $calculatedViews = 0;

    /**
     * @ORM\Column(name="calculated_unique_views", type="integer", length=11)
     */
    protected $calculatedUniqueViews = 0;

    /**
     * @ORM\Column(name="calculated_orders", type="integer", length=11)
     */
    protected $calculatedOrders = 0;

    /**
     * @ORM\Column(name="calculated_unique_orders", type="integer", length=11)
     */
    protected $calculatedUniqueOrders = 0;

    /**
     * @ORM\Column(name="calculated_conversion", type="float", scale=2)
     */
    protected $calculatedConversion = 0;

    /**
     * @ORM\Column(name="calculated_total", type="integer", length=11)
     */
    protected $calculatedTotal = 0;

    /**
     * @ORM\Column(name="calculated_score", type="integer", length=11)
     */
    protected $calculatedScore = 0;

    /**
     * @ORM\Column(name="is_cheat", type="integer", length=11)
     */
    protected $isCheat = 0;

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
     * Set calculatedViews
     *
     * @param integer $calculatedViews
     *
     * @return ProductStatistics
     */
    public function setCalculatedViews($calculatedViews)
    {
        $this->calculatedViews = $calculatedViews;

        return $this;
    }

    /**
     * Get calculatedViews
     *
     * @return integer
     */
    public function getCalculatedViews()
    {
        return $this->calculatedViews;
    }

    /**
     * Set calculatedUniqueViews
     *
     * @param integer $calculatedUniqueViews
     *
     * @return ProductStatistics
     */
    public function setCalculatedUniqueViews($calculatedUniqueViews)
    {
        $this->calculatedUniqueViews = $calculatedUniqueViews;

        return $this;
    }

    /**
     * Get calculatedUniqueViews
     *
     * @return integer
     */
    public function getCalculatedUniqueViews()
    {
        return $this->calculatedUniqueViews;
    }

    /**
     * Set calculatedOrders
     *
     * @param integer $calculatedOrders
     *
     * @return ProductStatistics
     */
    public function setCalculatedOrders($calculatedOrders)
    {
        $this->calculatedOrders = $calculatedOrders;

        return $this;
    }

    /**
     * Get calculatedOrders
     *
     * @return integer
     */
    public function getCalculatedOrders()
    {
        return $this->calculatedOrders;
    }

    /**
     * Set calculatedUniqueOrders
     *
     * @param integer $calculatedUniqueOrders
     *
     * @return ProductStatistics
     */
    public function setCalculatedUniqueOrders($calculatedUniqueOrders)
    {
        $this->calculatedUniqueOrders = $calculatedUniqueOrders;

        return $this;
    }

    /**
     * Get calculatedUniqueOrders
     *
     * @return integer
     */
    public function getCalculatedUniqueOrders()
    {
        return $this->calculatedUniqueOrders;
    }

    /**
     * Set calculatedConversion
     *
     * @param float $calculatedConversion
     *
     * @return ProductStatistics
     */
    public function setCalculatedConversion($calculatedConversion)
    {
        $this->calculatedConversion = $calculatedConversion;

        return $this;
    }

    /**
     * Get calculatedConversion
     *
     * @return float
     */
    public function getCalculatedConversion()
    {
        return $this->calculatedConversion;
    }

    /**
     * Set isCheat
     *
     * @param integer $isCheat
     *
     * @return ProductStatistics
     */
    public function setIsCheat($isCheat)
    {
        $this->isCheat = $isCheat;

        return $this;
    }

    /**
     * Get isCheat
     *
     * @return integer
     */
    public function getIsCheat()
    {
        return $this->isCheat;
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

    /**
     * Set calculatedScore
     *
     * @param integer $calculatedScore
     *
     * @return ProductStatistics
     */
    public function setCalculatedScore($calculatedScore)
    {
        $this->calculatedScore = $calculatedScore;

        return $this;
    }

    /**
     * Get calculatedScore
     *
     * @return integer
     */
    public function getCalculatedScore()
    {
        return $this->calculatedScore;
    }

    /**
     * Set calculatedTotal
     *
     * @param integer $calculatedTotal
     *
     * @return ProductStatistics
     */
    public function setCalculatedTotal($calculatedTotal)
    {
        $this->calculatedTotal = $calculatedTotal;

        return $this;
    }

    /**
     * Get calculatedTotal
     *
     * @return integer
     */
    public function getCalculatedTotal()
    {
        return $this->calculatedTotal;
    }
}
