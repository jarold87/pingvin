<?php

namespace CronBundle\Import\Component;

use AppBundle\Service\Setting;
use CronBundle\Import\Component\ClientAdapter\ClientAdapter;
use CronBundle\Import\Component\ItemListCollector\ItemListCollector;
use CronBundle\Import\Component\ItemCollector\ItemCollector;

class ComponentFactory
{
    /** @var ClientAdapter */
    protected $clientAdapter;

    /** @var ItemListCollector */
    protected $itemListCollector;

    /** @var ItemCollector */
    protected $itemCollector;

    /** @var RequestModel */
    protected $requestModel;

    /** @var ResponseDataConverter */
    protected $responseDataConverter;

    /** @var AllowanceValidator */
    protected $allowanceValidator;

    /** @var EntityObjectSetter */
    protected $entityObjectSetter;

    /**
     * @return ClientAdapter
     */
    public function getClientAdapter()
    {
        return $this->clientAdapter;
    }

    /**
     * @return ItemListCollector
     */
    public function getItemListCollector()
    {
        return $this->itemListCollector;
    }

    /**
     * @return ItemCollector
     */
    public function getItemCollector()
    {
        return $this->itemCollector;
    }

    /**
     * @return RequestModel
     */
    public function getRequestModel()
    {
        return $this->requestModel;
    }

    /**
     * @return ResponseDataConverter
     */
    public function getResponseDataConverter()
    {
        return $this->responseDataConverter;
    }

    /**
     * @return AllowanceValidator
     */
    public function getAllowanceValidator()
    {
        return $this->allowanceValidator;
    }

    /**
     * @return EntityObjectSetter
     */
    public function getEntityObjectSetter()
    {
        return $this->entityObjectSetter;
    }
}