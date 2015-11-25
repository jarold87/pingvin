<?php

namespace ShoprenterBundle\Import\AllowanceValidator;

use CronBundle\Import\AllowanceValidator;


class ProductAllowanceValidator extends AllowanceValidator
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
        if (!$this->existSku()) {
            $this->isAllowed = false;
        }
        return parent::isAllowed();
    }

    /**
     * @return bool
     */
    protected function existSku()
    {
        return $this->existColumn('sku');
    }
}