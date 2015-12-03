<?php

namespace GoogleApiBundle\Import;

use CronBundle\Import\ClientAdapter as ClientAdapterAbstract;
use CronBundle\Import\ClientAdapterInterface;
use GoogleApiBundle\Services\AnalyticsService;

class ClientAdapter extends ClientAdapterAbstract implements ClientAdapterInterface
{
    /** @var AnalyticsService */
    protected $analyticsService;

    /** @var array */
    protected $response = array();

    /**
     * @param $service
     */
    public function setAnalyticsService(AnalyticsService $service)
    {
        $this->analyticsService = $service;
    }

    public function init()
    {
        $id = $this->settingService->get('ga_profile_id');
        $this->analyticsService->setProfileId($id);
        parent::init();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getCollectionRequest($request)
    {
        $this->resetResponse();
        $this->addRequestCount();
        $response = $this->analyticsService->getRequest($request);
        if ($response) {
            $this->response = $response;
        }
        return parent::getCollectionRequest($request);
    }

    /**
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function getRequest($request)
    {
        $this->resetResponse();
        try {
            $this->addRequestCount();
            $response = '';
            if ($response) {
                $this->response = $response;
            }
            return parent::getRequest($request);
        }
        catch ( \Exception $e ) {
            throw new \Exception("Not a valid query!");
        }
    }

    protected function resetResponse()
    {
        $this->response = array();
    }
}