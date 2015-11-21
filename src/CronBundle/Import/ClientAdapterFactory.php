<?php

namespace CronBundle\Import;

use ShoprenterBundle\Import\ClientAdapter as SrClient;
use AppBundle\Service\Setting;

class ClientAdapterFactory
{
    /** @var Setting */
    protected $settingService;

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
     * @return SrClient|void
     */
    public function getClientAdapter()
    {
        switch ($this->settingService->get('shop_type')) {
            case $this->srId:
                return new SrClient($this->settingService);
                break;
            case $this->shopifyId:
                return;
                break;
            default:
                return false;
        }
    }
}