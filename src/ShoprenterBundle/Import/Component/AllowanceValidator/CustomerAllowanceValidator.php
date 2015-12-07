<?php

namespace ShoprenterBundle\Import\Component\AllowanceValidator;

use CronBundle\Import\Component\AllowanceValidator;


class CustomerAllowanceValidator extends AllowanceValidator
{
    /**
     * @return mixed
     */
    public function isAllowed()
    {
        $this->isAllowed = true;
        if (!$this->existOuterId()) {
            $this->isAllowed = false;
        }
        return parent::isAllowed();
    }
}