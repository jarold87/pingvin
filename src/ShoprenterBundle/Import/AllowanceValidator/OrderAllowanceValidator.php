<?php

namespace ShoprenterBundle\Import\AllowanceValidator;

use CronBundle\Import\AllowanceValidator;


class OrderAllowanceValidator extends AllowanceValidator
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