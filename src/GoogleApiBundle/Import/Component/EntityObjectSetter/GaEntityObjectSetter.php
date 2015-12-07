<?php

namespace GoogleApiBundle\Import\Component\EntityObjectSetter;

use CronBundle\Import\Component\EntityObjectSetter;

class GaEntityObjectSetter extends EntityObjectSetter
{
    /** @var string */
    protected $timeKey = '';

    /**
     * @param $key
     */
    public function setTimeKey($key)
    {
        $this->timeKey = $key;
    }
}