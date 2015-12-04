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
        if (!$id) {
            $this->error = 'Missing GA profil ID!';
            return;
        }
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
        if ($this->analyticsService->getError()) {
            $this->error = $this->analyticsService->getError();
            return;
        }
        if (!$response) {
            $this->error = 'Missing GA response!';
            return;
        }
        $this->response = $response;
        return parent::getCollectionRequest($request);
    }

    /**
     * @param $request
     * @throws \Exception
     */
    public function getRequest($request)
    {
        throw new \Exception("Not a valid query!");
    }

    protected function resetResponse()
    {
        $this->response = array();
    }
}