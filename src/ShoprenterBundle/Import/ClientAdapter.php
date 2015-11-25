<?php

namespace ShoprenterBundle\Import;

use ShoprenterBundle\Import\ApiClient\ApiCall;
use CronBundle\Import\ClientAdapter as ClientAdapterAbstract;
use CronBundle\Import\ClientAdapterInterface;

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
            throw new \Exception("Not a valid database connection!");
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
        try {
            $this->addRequestCount();
            $response = $this->db->query($request)->fetchAll();
            if ($response) {
                $this->response = $response;
            }
            return parent::getCollectionRequest($request);
        }
        catch ( \Exception $e ) {
            throw new \Exception("Not a valid query!");
        }
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
            $response = $this->db->query($request)->fetch();
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