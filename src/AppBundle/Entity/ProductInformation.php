<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_information")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductInformation
{
    /**
     * @ORM\Id
     * @ORM\Column(name="information_id", type="integer", length=11)
     * @ORM\GeneratedValue
     */
    protected $informationId = null;

    /**
     * @ORM\Column(name="product_id", type="string", length=50)
     */
    protected $product_id = null;

    /**
     * @ORM\Column(name="information_key", type="string", length=50)
     */
    protected $informationKey = null;

    /**
     * @ORM\Column(name="information_value", type="string", length=255)
     */
    protected $informationValue = null;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_information")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     */
    protected $product;

    /**
     * Get informationId
     *
     * @return integer
     */
    public function getInformationId()
    {
        return $this->informationId;
    }

    /**
     * Set productId
     *
     * @param string $productId
     *
     * @return ProductInformation
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set informationKey
     *
     * @param string $informationKey
     *
     * @return ProductInformation
     */
    public function setInformationKey($informationKey)
    {
        $this->informationKey = $informationKey;

        return $this;
    }

    /**
     * Get informationKey
     *
     * @return string
     */
    public function getInformationKey()
    {
        return $this->informationKey;
    }

    /**
     * Set informationValue
     *
     * @param string $informationValue
     *
     * @return ProductInformation
     */
    public function setInformationValue($informationValue)
    {
        $this->informationValue = $informationValue;

        return $this;
    }

    /**
     * Get informationValue
     *
     * @return string
     */
    public function getInformationValue()
    {
        return $this->informationValue;
    }

    /**
     * Set product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return ProductInformation
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
