<?php
namespace AppBundle\Report;

use Doctrine\ORM\EntityManager;
use AppBundle\Service\Setting;

class Report
{
    /** @var string */
    protected $timeKey;

    /** @var Setting */
    protected $settingService;

    /** @var EntityManager */
    protected $entityManager;

    /** @var array */
    protected $rowsToReport = array();

    /** @var array */
    protected $collectedData = array();

    /** @var array */
    protected $list = array();

    /**
     * @param Setting $service
     */
    public function setSettingService(Setting $service)
    {
        $this->settingService = $service;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->reset();
        $this->entityManager = $entityManager;
    }

    /**
     * @param $key
     */
    public function setTimeKey($key)
    {
        $this->timeKey = $key;
    }

    protected function reset()
    {
        $this->list = array();
        $this->rowsToReport = array();
        $this->collectedData = array();
    }

    protected function setRowsToReport()
    {
        if (!$this->collectedData) {
            return;
        }
        $this->rowsToReport = $this->collectedData;
    }
}