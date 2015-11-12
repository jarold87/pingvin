<?php

namespace CronBundle\Import;

use CronBundle\Import\Shop\Sr\ClientAdapter as SrClient;
use CronBundle\Import\Shop\Shopify\ClientAdapter as ShopifyClient;
use AppBundle\Service\Setting;

class ClientAdapterFactory
{
    /** @var Setting */
    protected $settingService;

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $entityManager;

    /** @var string */
    protected $srId = 'SR';

    /** @var string */
    protected $shopifyId = 'Shopify';

    /**
     * @param Setting $settingService
     */
    public function __construct(Setting $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * @return ShopifyClient|SrClient|void
     */
    public function getClientAdapter()
    {
        switch ($this->settingService->get('shop_type')) {
            case $this->srId:
                return new SrClient($this->settingService);
                break;
            case $this->shopifyId:
                return new ShopifyClient($this->settingService);
                break;
            default:
                return false;
        }
    }
}