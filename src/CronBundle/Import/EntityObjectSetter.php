<?php

namespace CronBundle\Import;


abstract class EntityObjectSetter
{
    protected $object;

    /** @var array */
    protected $informationObject = array();
    
    /** @var array */
    protected $data = array();

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
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return array
     */
    public function getInformationObjects()
    {
        return $this->informationObject;
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
            case 'date':
                return (isset($this->data[$key])) ? new \DateTime($this->data[$key]) : new \DateTime();
        }
        return '';
    }
}