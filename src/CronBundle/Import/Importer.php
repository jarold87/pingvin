<?php

namespace CronBundle\Import;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use AppBundle\Service\Setting;
use CronBundle\Service\ImportLog;
use CronBundle\Service\RuntimeWatcher;
use CronBundle\Import\Component\ComponentFactory;
use CronBundle\Import\Component\ClientAdapter\ClientAdapter;
use GoogleApiBundle\Service\AnalyticsService;
use CronBundle\Import\Component\RequestModel;
use CronBundle\Import\Component\ResponseDataConverter;
use CronBundle\Import\Component\AllowanceValidator;
use CronBundle\Import\Component\EntityObjectSetter;
use CronBundle\Import\Component\ItemListCollector\ItemListCollector;
use CronBundle\Import\Component\ItemCollector\ItemCollector;

abstract class Importer
{
    /** @var string */
    protected $importName;

    /** @var string */
    protected $entityName;

    /** @var string */
    protected $outerIdKey;

    /** @var Setting */
    protected $settingService;

    /** @var AnalyticsService */
    protected $analyticsService;

    /** @var EntityManager */
    protected $entityManager;

    /** @var ComponentFactory */
    protected $componentFactory;

    /** @var ClientAdapter */
    protected $clientAdapter;

    /** @var RuntimeWatcher */
    protected $runtimeWatcher;

    /** @var ImportLog */
    protected $importLog;

    /** @var RequestModel */
    protected $requestModel;

    /** @var ResponseDataConverter */
    protected $responseDataConverter;

    /** @var AllowanceValidator */
    protected $allowanceValidator;

    /** @var EntityObjectSetter */
    protected $entityObjectSetter;

    /** @var ArrayCollection */
    protected $itemProcessCollection;

    /** @var ItemListCollector */
    protected $itemListCollector;

    /** @var ItemCollector */
    protected $itemCollector;

    /** @var int */
    protected $counterToFlush = 0;

    /** @var int */
    protected $lastItemIndex = 1;

    /** @var array */
    private $error = array();

    /**
     * @param $importName
     */
    public function setImportName($importName)
    {
        $this->importName = $importName;
    }

    /**
     * @param Setting $setting
     */
    public function setSettingService(Setting $setting)
    {
        $this->settingService = $setting;
    }

    /**
     * @param AnalyticsService $service
     */
    public function setAnalyticsService(AnalyticsService $service)
    {
        $this->analyticsService = $service;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ComponentFactory $factory
     */
    public function setComponentFactory(ComponentFactory $factory)
    {
        $this->componentFactory = $factory;
    }

    /**
     * @param $runtimeWatcher
     */
    public function setRuntimeWatcher(RuntimeWatcher $runtimeWatcher)
    {
        $this->runtimeWatcher = $runtimeWatcher;
    }

    /**
     * @param ImportLog $service
     */
    public function setImportLog(ImportLog $service)
    {
        $this->importLog = $service;
    }

    /**
     * @return array|bool
     */
    public function getError()
    {
        if ($this->error) {
            return $this->error;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isFinishedImport()
    {
        if (!$this->itemCollector->isFinishedItemCollect()) {
            return false;
        }
        if ($this->isTimeOut()) {
            return false;
        }
        if ($this->getError()) {
            return false;
        }
        return true;
    }

    public function init()
    {
        throw new \Exception("Not a valid importer init!");
    }

    public function import()
    {
        throw new \Exception("Not a valid importer!");
    }

    protected function initItemProcessCollection()
    {
        $this->itemProcessCollection = new ArrayCollection();
    }

    protected function initClientAdapter()
    {
        $this->clientAdapter = $this->componentFactory->getClientAdapter();
        $this->clientAdapter->setSettingService($this->settingService);
        $this->clientAdapter->setImportLog($this->importLog);
    }

    protected function initRequestModel()
    {
        $this->requestModel = $this->componentFactory->getRequestModel();
    }

    protected function initResponseDataConverter()
    {
        $this->responseDataConverter = $this->componentFactory->getResponseDataConverter();
    }

    protected function initAllowanceValidator()
    {
        $this->allowanceValidator = $this->componentFactory->getAllowanceValidator();
    }

    protected function initEntityObjectSetter()
    {
        $this->entityObjectSetter = $this->componentFactory->getEntityObjectSetter();
    }

    protected function initItemListCollector()
    {
        $this->itemListCollector = $this->componentFactory->getItemListCollector();
        $this->itemListCollector->setEntityManager($this->entityManager);
        $this->itemListCollector->setImportLog($this->importLog);
        $this->itemListCollector->setRuntimeWatcher($this->runtimeWatcher);
        $this->itemListCollector->setEntityName($this->entityName);
        $this->itemListCollector->setImportName($this->importName);
        $this->itemListCollector->setOuterIdKey($this->outerIdKey);
    }

    protected function initItemCollector()
    {
        $this->itemCollector = $this->componentFactory->getItemCollector();
        $this->itemCollector->setEntityManager($this->entityManager);
        $this->itemCollector->setImportLog($this->importLog);
        $this->itemCollector->setRuntimeWatcher($this->runtimeWatcher);
        $this->itemCollector->setEntityName($this->entityName);
        $this->itemCollector->setImportName($this->importName);
        $this->itemCollector->setOuterIdKey($this->outerIdKey);
        $this->itemCollector->setEntityObjectSetter($this->entityObjectSetter);}

    protected function collectItems()
    {
        if (!$this->isInLimits()) {
            return;
        }
        $this->itemListCollector->init();
        $this->itemListCollector->collect();
        if ($this->itemListCollector->getError()) {
            $this->addError($this->itemListCollector->getError());
        }
        $this->lastItemIndex = $this->itemListCollector->getLastItemIndex();
    }

    protected function collectItemData()
    {
        if (!$this->isInLimits()) {
            return;
        }
        $this->itemCollector->init();
        $this->itemCollector->collect();
        if ($this->itemCollector->getError()) {
            $this->addError($this->itemCollector->getError());
        }
        $this->lastItemIndex = $this->itemCollector->getLastItemIndex();
    }

    /**
     * @return bool
     */
    protected function isTimeOut()
    {
        return $this->runtimeWatcher->isTimeOut();
    }

    /**
     * @param $error
     */
    protected function addError($error)
    {
        $this->error[] = $error;
    }

    /**
     * @return bool
     */
    protected function isInLimits()
    {
        return $this->runtimeWatcher->isInTimeLimit();
    }

    protected function saveImportLog()
    {
        $this->importLog->setUserLastIndex($this->lastItemIndex);
        $this->importLog->setUnProcessItemCount($this->itemCollector->getUnProcessItemCount());
        $this->importLog->setRuntime($this->runtimeWatcher->getRuntime());
        $error = $this->getError();
        $this->importLog->setError($error[0]);
        $log = $this->importLog->getUserLog();
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}