<?php

namespace CronBundle\Import\Shop\Shopify;

use CronBundle\Import\Shop\Products as ShopProducts;

class Products extends ShopProducts
{
    public function import()
    {
        echo '<hr>Shopify 1<hr>';
    }
}