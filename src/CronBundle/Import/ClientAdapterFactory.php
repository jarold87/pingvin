<?php

namespace CronBundle\Import;

use ShoprenterBundle\Import\ClientAdapter as SrClient;
use AppBundle\Service\Setting;

class ClientAdapterFactory
{
    /** @var Setting */
    protected $settingService;

    /** @var string */
    protected $shoprenterId = 'SR';

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
     * @return SrClient
     */
    public function getClientAdapter()
    {
        switch ($this->settingService->get('shop_type')) {
            case $this->shoprenterId:
                return new SrClient($this->settingService);
            case $this->shopifyId:
                return;
        }
    }
}