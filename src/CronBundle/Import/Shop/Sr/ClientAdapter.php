<?php

namespace CronBundle\Import\Shop\Sr;

use CronBundle\Import\Shop\Sr\ApiClient\ApiCall;
use CronBundle\Import\Shop\ClientAdapter as ClientAdapterAbstract;
use CronBundle\Import\Shop\ClientAdapterInterface;

class ClientAdapter extends ClientAdapterAbstract implements ClientAdapterInterface
{
    /** @var  ApiCall */
    protected $apiCall;

    /** @var string */
    protected $url = '';

    public function init()
    {
        $this->apiCall = $this->getApiCall();
        $this->url = $this->settingService->get('shop_api_url');
    }

    /**
     * @param $request
     * @return array|null
     * @throws ApiClient\Exception
     */
    public function getCollectionRequest($request)
    {
        $response = $this->apiCall->execute('GET', $this->url . '/' . $request);
        if ($response) {
            return $response->getParsedResponseBody();
        }
        return null;
    }

    /**
     * @param $request
     * @return array|null
     * @throws ApiClient\Exception
     */
    public function getRequest($request)
    {
        $response = $this->apiCall->execute('GET', $request);
        if ($response) {
            return $response->getParsedResponseBody();
        }
        return null;
    }

    /**
     * @return ApiCall
     */
    protected function getApiCall()
    {
        if (!$this->existApiCall()) {
            $this->apiCall = new ApiCall(
                $this->settingService->get('shop_api_user'),
                $this->settingService->get('shop_api_password')
            );
            $this->apiCall->setFormat('json');
        }
        return $this->apiCall;
    }

    /**
     * @return bool
     */
    protected function existApiCall()
    {
        if ($this->apiCall) {
            return true;
        }
        return false;
    }
}