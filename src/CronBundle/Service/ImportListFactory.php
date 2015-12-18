<?php

namespace CronBundle\Service;

use AppBundle\Service\Setting;
use ShoprenterBundle\Import\ImportList as SrImportList;

class ImportListFactory
{
    /** @var Setting */
    protected $settingService;

    /** @var string */
    protected $ShopRenterId;

    /** @var string */
    protected $ShopifyId;

    /**
     * @param Setting $settingService
     */
    public function setSettingService(Setting $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * @param $id
     */
    public function setShopRenterId($id)
    {
        $this->ShopRenterId = $id;
    }

    /**
     * @param $id
     */
    public function setShopifyId($id)
    {
        $this->ShopifyId = $id;
    }

    /**
     * @return SrImportList
     */
    public function getImportList()
    {
        switch ($this->settingService->get('shop_type')) {
            case $this->ShopRenterId:
                return new SrImportList();
        }
    }
}