<?php

namespace CronBundle\Import;

use AppBundle\Service\Setting;
use ShoprenterBundle\Import\ImportList as SrImportList;

class ImportListFactory
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
     * @return SrImportList
     */
    public function getImportList()
    {
        switch ($this->settingService->get('shop_type')) {
            case $this->shoprenterId:
                return new SrImportList();
            case $this->shopifyId:
                return;
        }
    }
}