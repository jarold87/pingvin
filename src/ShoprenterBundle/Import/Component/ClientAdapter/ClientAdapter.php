<?php

namespace ShoprenterBundle\Import\Component\ClientAdapter;

use CronBundle\Import\Component\ClientAdapter\ClientAdapter as ClientAdapterAbstract;
use CronBundle\Import\Component\ClientAdapter\ClientAdapterInterface;

class ClientAdapter extends ClientAdapterAbstract implements ClientAdapterInterface
{
    /** @var \PDO */
    protected $db;

    /** @var array */
    protected $response = array();

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
        catch ( \Exception $e ) {
            $this->error = "Not a valid database connection!";
            return;
        }
        parent::init();
    }

    /**
     * @param $request
     * @return mixed
     * @throws \Exception
     */
    public function getCollectionRequest($request)
    {
        $this->resetResponse();
        $this->addShopRequestCount();
        $query = $this->db->query($request);
        if (!$query) {
            $errorInfo = $this->db->errorInfo();
            $this->error = "Not a valid collection request! : " . $errorInfo[2];
            return;
        }
        $response = $query->fetchAll();
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
        $this->addShopRequestCount();
        $query = $this->db->query($request);
        if (!$query) {
            $errorInfo = $this->db->errorInfo();
            $this->error = "Not a valid item request! : " . $errorInfo[2];
            return;
        }
        $response = $query->fetch();
        if ($response) {
            $this->response = $response;
        }
        return parent::getRequest($request);
    }

    /**
     * @param $request
     * @return mixed|void
     */
    public function getPackageRequest($request)
    {
        $this->resetResponse();
        $this->addShopRequestCount();
        $query = $this->db->query($request);
        if (!$query) {
            $errorInfo = $this->db->errorInfo();
            $this->error = "Not a valid item package request! : " . $errorInfo[2];
            return;
        }
        $response = $query->fetchAll();
        if ($response) {
            $this->response = $response;
        }
        return parent::getPackageRequest($request);
    }

    protected function resetResponse()
    {
        $this->response = array();
    }
}