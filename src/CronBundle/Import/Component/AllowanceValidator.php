<?php

namespace CronBundle\Import\Component;


abstract class AllowanceValidator
{
    /** @var array */
    protected $data = array();

    /** @var bool */
    protected $isAllowed = true;

    /** @var string */
    protected $outerIdKey = 'outerId';

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
    public function isAllowed()
    {
        return $this->isAllowed;
    }


    /**
     * @return bool
     */
    protected function existOuterId()
    {
        return $this->existColumn($this->outerIdKey);
    }

    /**
     * @param $column
     * @return bool
     */
    protected function existColumn($column)
    {
        if (!isset($this->data[$column])) {
            return false;
        }
        if (!$this->data[$column]) {
            return false;
        }
        return true;
    }
}