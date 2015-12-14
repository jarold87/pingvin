<?php

namespace ShoprenterBundle\Import\Component\EntityObjectSetter;

use CronBundle\Import\Component\CalculateMetricsEntityObjectSetter;
use AppBundle\Entity\ProductStatistics;


class ProductCalculateMetricsEntityObjectSetter extends CalculateMetricsEntityObjectSetter
{
    /** @var ProductStatistics */
    protected $object;

    /**
     * @return ProductStatistics
     */
    public function getObject()
    {
        $this->object->setViews($this->getFormattedData('views', 'integer'));
        $this->object->setUniqueViews($this->getFormattedData('uniqueViews', 'integer'));
        $this->object->setOrders($this->getFormattedData('orders', 'integer'));
        $this->object->setUniqueOrders($this->getFormattedData('uniqueOrders', 'integer'));
        $this->object->setConversion($this->getFormattedData('conversion', 'integer'));
        $this->object->setIsCheat($this->getFormattedData('isCheat', 'integer'));
        return parent::getObject();
    }
}