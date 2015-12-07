<?php

namespace CronBundle\Import\Component;

class ShopEntityObjectSetter extends EntityObjectSetter
{
    public function getObject()
    {
        $this->object->setIsDead(0);
        return parent::getObject();
    }
}