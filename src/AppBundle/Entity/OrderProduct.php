<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shop_order_product")
 * @ORM\HasLifecycleCallbacks()
 */
class OrderProduct
{
    /**
     * @ORM\Id
     * @ORM\Column(name="order_product_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $orderProductId = null;

    /**
     * @ORM\Column(name="outer_id", type="string", length=100)
     */
    protected $outerId = null;

    /**
     * @ORM\Column(name="order_outer_id", type="string", length=100)
     */
    protected $orderOuterId = null;

    /**
     * @ORM\Column(name="product_outer_id", type="string", length=100)
     */
    protected $productOuterId = null;

    /**
     * @ORM\Column(name="quantity", type="integer", length=11)
     */
    protected $quantity = null;

    /**
     * @ORM\Column(name="total", type="float", scale=2)
     */
    protected $total = null;

    /**
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate = null;

    /**
     * @ORM\Column(name="update_date", type="datetime")
     */
    protected $updateDate = null;

    /**
     * Get orderProductId
     *
     * @return integer
     */
    public function getOrderProductId()
    {
        return $this->orderProductId;
    }

    /**
     * Set outerId
     *
     * @param string $outerId
     *
     * @return OrderProduct
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
     * Set orderOuterId
     *
     * @param string $orderOuterId
     *
     * @return OrderProduct
     */
    public function setOrderOuterId($orderOuterId)
    {
        $this->orderOuterId = $orderOuterId;

        return $this;
    }

    /**
     * Get orderOuterId
     *
     * @return string
     */
    public function getOrderOuterId()
    {
        return $this->orderOuterId;
    }

    /**
     * Set productOuterId
     *
     * @param string $productOuterId
     *
     * @return OrderProduct
     */
    public function setProductOuterId($productOuterId)
    {
        $this->productOuterId = $productOuterId;

        return $this;
    }

    /**
     * Get productOuterId
     *
     * @return string
     */
    public function getProductOuterId()
    {
        return $this->productOuterId;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrderProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return OrderProduct
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
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
}
