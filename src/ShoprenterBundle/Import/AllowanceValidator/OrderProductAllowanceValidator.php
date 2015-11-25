<?php

namespace ShoprenterBundle\Import\AllowanceValidator;

use CronBundle\Import\AllowanceValidator;


class OrderProductAllowanceValidator extends AllowanceValidator
{
    /**
     * @return mixed
     */
    public function isAllowed()
    {
        if (!$this->existOuterId()) {
            $this->isAllowed = false;
        }
        return parent::isAllowed();
    }
}