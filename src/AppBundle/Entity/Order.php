<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shop_order")
 * @ORM\HasLifecycleCallbacks()
 */
class Order extends Shop
{
    /**
     * @ORM\Id
     * @ORM\Column(name="order_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $orderId = null;

    /**
     * @ORM\Column(name="outer_id", type="string", length=100)
     */
    protected $outerId = null;

    /**
     * @ORM\Column(name="customer_outer_id", type="string", length=100)
     */
    protected $customerOuterId = null;

    /**
     * @ORM\Column(name="shipping_method", type="string", length=255)
     */
    protected $shippingMethod = null;

    /**
     * @ORM\Column(name="payment_method", type="string", length=255)
     */
    protected $paymentMethod = null;

    /**
     * @ORM\Column(name="currency", type="string", length=20)
     */
    protected $currency = null;

    /**
     * @ORM\Column(name="order_date", type="datetime")
     */
    protected $orderDate = null;

    /**
     * @ORM\OneToMany(targetEntity="OrderProduct", mappedBy="order")
     */
    protected $orderProducts;

    /**
     * Get orderId
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set outerId
     *
     * @param string $outerId
     *
     * @return Order
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
     * Set customerOuterId
     *
     * @param string $customerOuterId
     *
     * @return Order
     */
    public function setCustomerOuterId($customerOuterId)
    {
        $this->customerOuterId = $customerOuterId;

        return $this;
    }

    /**
     * Get customerOuterId
     *
     * @return string
     */
    public function getCustomerOuterId()
    {
        return $this->customerOuterId;
    }

    /**
     * Set shippingMethod
     *
     * @param string $shippingMethod
     *
     * @return Order
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    /**
     * Get shippingMethod
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     *
     * @return Order
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Order
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set orderDate
     *
     * @param \DateTime $orderDate
     *
     * @return Order
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
     * Constructor
     */
    public function __construct()
    {
        $this->orderProducts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add orderProduct
     *
     * @param \AppBundle\Entity\OrderProduct $orderProduct
     *
     * @return Order
     */
    public function addOrderProduct(\AppBundle\Entity\OrderProduct $orderProduct)
    {
        $this->orderProducts[] = $orderProduct;

        return $this;
    }

    /**
     * Remove orderProduct
     *
     * @param \AppBundle\Entity\OrderProduct $orderProduct
     */
    public function removeOrderProduct(\AppBundle\Entity\OrderProduct $orderProduct)
    {
        $this->orderProducts->removeElement($orderProduct);
    }

    /**
     * Get orderProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderProducts()
    {
        return $this->orderProducts;
    }
}
