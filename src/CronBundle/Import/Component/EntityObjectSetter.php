<?php

namespace CronBundle\Import\Component;

use Doctrine\ORM\EntityManager;

abstract class EntityObjectSetter
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var */
    protected $object;
    
    /** @var array */
    protected $data = array();

    /**
     * @param $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function setDataToObject()
    {
        if ($this->object) {
            $this->entityManager->persist($this->object);
        }
    }
    
    /**
     * @param $key
     * @param $type
     * @return \DateTime|int|string
     */
    protected function getFormattedData($key, $type)
    {
        switch ($type) {
            case 'string':
                return (isset($this->data[$key])) ? $this->data[$key] : '';
            case 'integer':
                return (isset($this->data[$key])) ? $this->data[$key] : 0;
            case 'float':
                return (isset($this->data[$key])) ? $this->data[$key] : 0;
            case 'date':
                return (isset($this->data[$key])) ? new \DateTime($this->data[$key]) : new \DateTime();
        }
        return '';
    }
}