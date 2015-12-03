<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shop_order_product")
 * @ORM\HasLifecycleCallbacks()
 */
class OrderProduct extends Shop
{
    /**
     * @ORM\Id
     * @ORM\Column(name="order_product_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $orderProductId = null;

    /**
     * @ORM\Column(name="order_id", type="string", length=100)
     */
    protected $orderId = null;

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
     * @ORM\Column(name="order_date", type="datetime")
     */
    protected $orderDate = null;

    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="orderProducts")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="order_id")
     */
    protected $order;

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
     * Set orderDate
     *
     * @param \DateTime $orderDate
     *
     * @return OrderProduct
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    /**
     * Get orderDate
     *
     * @return \DateTime
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * Set order
     *
     * @param \AppBundle\Entity\Order $order
     *
     * @return OrderProduct
     */
    public function setOrder(\AppBundle\Entity\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \AppBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set orderId
     *
     * @param string $orderId
     *
     * @return OrderProduct
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
}
