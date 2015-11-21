<?php

namespace ShoprenterBundle\Import;

use ShoprenterBundle\Import\ApiClient\ApiCall;
use CronBundle\Import\ClientAdapter as ClientAdapterAbstract;
use CronBundle\Import\ClientAdapterInterface;

class ClientAdapter extends ClientAdapterAbstract implements ClientAdapterInterface
{
    /** @var \PDO */
    protected $db;

    public function init()
    {
        $host = $this->settingService->get('shop_database_host');
        $db = $this->settingService->get('shop_database_name');
        $user = $this->settingService->get('shop_database_user');
        $pass = $this->settingService->get('shop_database_password');

        try {
            $this->db = new \PDO(
                'mysql:dbname=' . $db . ';host=' . $host,
                $user,
                $pass,
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
            );
        }
        catch ( Exception $e ) {
            throw new Exception("Not a valid database connection!");
        }
    }

    /**
     * @param $request
     * @return array
     * @throws Exception
     */
    public function getCollectionRequest($request)
    {
        try {
            return $this->db->query($request)->fetchAll();
        }
        catch ( Exception $e ) {
            throw new Exception("Not a valid query!");
        }
    }

    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public function getRequest($request)
    {
        try {
            return $this->db->query($request)->fetch();
        }
        catch ( Exception $e ) {
            throw new Exception("Not a valid query!");
        }
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