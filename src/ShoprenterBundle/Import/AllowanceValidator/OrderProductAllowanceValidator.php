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
        $this->isAllowed = true;
        if (!$this->existOuterId()) {
            $this->isAllowed = false;
        }
        if (!$this->existOrderOuterId()) {
            $this->isAllowed = false;
        }
        if (!$this->existProductOuterId()) {
            $this->isAllowed = false;
        }
        return parent::isAllowed();
    }

    /**
     * @return bool
     */
    protected function existOrderOuterId()
    {
        return $this->existColumn('orderOuterId');
    }

    /**
     * @return bool
     */
    protected function existProductOuterId()
    {
        return $this->existColumn('productOuterId');
    }
}