<?php

namespace ShoprenterBundle\Import\Component\EntityObjectSetter;

use CronBundle\Import\Component\CalculateMetricsEntityObjectSetter;
use AppBundle\Entity\ProductStatistics;
use AppBundle\Entity\Product;


class ProductCalculateMetricsEntityObjectSetter extends CalculateMetricsEntityObjectSetter
{
    /** @var ProductStatistics */
    protected $object;

    /**
     * @return ProductStatistics
     */
    public function setDataToObject()
    {
        $this->object->setCalculatedViews($this->getFormattedData('calculatedViews', 'integer'));
        $this->object->setCalculatedUniqueViews($this->getFormattedData('calculatedUniqueViews', 'integer'));
        $this->object->setCalculatedOrders($this->getFormattedData('calculatedOrders', 'integer'));
        $this->object->setCalculatedUniqueOrders($this->getFormattedData('calculatedUniqueOrders', 'integer'));
        $this->object->setCalculatedConversion($this->getFormattedData('calculatedConversion', 'integer'));
        $this->object->setCalculatedTotal($this->getFormattedData('calculatedTotal', 'integer'));
        $this->object->setCalculatedScore($this->getFormattedData('calculatedScore', 'integer'));
        $this->object->setIsCheat($this->getFormattedData('isCheat', 'integer'));
        parent::setDataToObject();
    }
}