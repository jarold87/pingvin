<?php

namespace CronBundle\Import;

use AppBundle\Service\Setting;
use GoogleApiBundle\Import\ClientAdapter as GoogleAnalyticsClientAdapter;
use GoogleApiBundle\Services\AnalyticsService;

class ClientAdapterFactory
{
    /** @var Setting */
    protected $settingService;

    /** @var string */
    protected $importSourceType = '';

    /** @var AnalyticsService */
    protected $analyticsService;

    /** @var string */
    protected $shop = 'shop';

    /** @var string */
    protected $ga = 'GA';

    /**
     * @param Setting $settingService
     * @param $importSourceType
     * @param AnalyticsService $analyticsService
     */
    public function __construct(Setting $settingService, $importSourceType, AnalyticsService $analyticsService)
    {
        $this->settingService = $settingService;
        $this->importSourceType = $importSourceType;
        $this->analyticsService = $analyticsService;
    }

    /**
     * @return GoogleAnalyticsClientAdapter|\ShoprenterBundle\Import\ClientAdapter
     */
    public function getClientAdapter()
    {
        switch ($this->importSourceType) {
            case $this->shop:
                $shopClientAdapterFactory = new ShopClientAdapterFactory($this->settingService);
                return $shopClientAdapterFactory->getClientAdapter();
            case $this->ga:
                $gaClient = new GoogleAnalyticsClientAdapter($this->settingService);
                $gaClient->setAnalyticsService($this->analyticsService);
                return $gaClient;
        }
    }
}