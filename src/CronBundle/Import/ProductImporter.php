<?php

namespace CronBundle\Import;

use AppBundle\Entity\Product;

class ProductImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'product';

    /** @var string */
    protected $entity = 'Product';

    /**
     * @param $data
     */
    protected function setProduct($data)
    {
        if (isset($this->existEntityKeyByOuterId[$data['outerId']])) {
            /** @var Product $product */
            $product = $this->existEntityCollection->get(
                $this->existEntityKeyByOuterId[$data['outerId']]
            );
            $product->setName((isset($data['name'])) ? $data['name'] : '');
            $product->setPicture((isset($data['picture'])) ? $data['picture'] : '');
            $product->setUrl((isset($data['url'])) ? $data['url'] : '');
            $product->setManufacturer((isset($data['manufacturer'])) ? $data['manufacturer'] : '');
            $product->setCategory((isset($data['category'])) ? $data['category'] : '');
            $product->setOuterId($data['outerId']);
            $product->setProductCreateDate((isset($data['productCreateDate'])) ? new \DateTime($data['productCreateDate']) : new \DateTime());
            return;
        }
        $product = new Product();
        $product->setSku($data['sku']);
        $product->setName((isset($data['name'])) ? $data['name'] : '');
        $product->setPicture((isset($data['picture'])) ? $data['picture'] : '');
        $product->setUrl((isset($data['url'])) ? $data['url'] : '');
        $product->setManufacturer((isset($data['manufacturer'])) ? $data['manufacturer'] : '');
        $product->setCategory((isset($data['category'])) ? $data['category'] : '');
        $product->setOuterId($data['outerId']);
        $product->setProductCreateDate((isset($data['productCreateDate'])) ? new \DateTime($data['productCreateDate']) : new \DateTime());
        $this->entityManager->persist($product);
    }
}