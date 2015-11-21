<?php

namespace CronBundle\Import;

use AppBundle\Service\Setting;


abstract class ClientAdapter
{
    /** @var Setting */
    protected $settingService;

    /**
     * @param Setting $settingService
     */
    public function __construct(Setting $settingService)
    {
        $this->settingService = $settingService;
    }
}