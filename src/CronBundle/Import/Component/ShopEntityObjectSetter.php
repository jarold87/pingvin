<?php

namespace CronBundle\Import\Component;

class ShopEntityObjectSetter extends EntityObjectSetter
{
    public function setDataToObject()
    {
        $this->object->setIsDead(0);
        parent::setDataToObject();
    }
}