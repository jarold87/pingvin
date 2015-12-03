<?php

namespace GoogleApiBundle\Import\EntityObjectSetter;

use CronBundle\Import\EntityObjectSetter;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductStatistics;

class ProductPageViewEntityObjectSetter extends EntityObjectSetter
{
    /** @var Product */
    protected $object;

    /** @var ProductStatistics */
    protected $statisticsObject;

    /** @var string */
    protected $timeKey = '';

    /**
     * @param $key
     */
    public function setTimeKey($key)
    {
        $this->timeKey = $key;
    }

    /**
     * @return Product
     */
    public function getObject()
    {
        $this->statisticsObject = null;
        $objects = $this->object->getProductStatistics();
        if ($objects->count()) {
            foreach ($objects->toArray() as $object) {
                if ($object->getTimeKey() == $this->timeKey) {
                    $this->statisticsObject = $object;
                }
            }
        }
        if (!$this->statisticsObject) {
            $this->statisticsObject = new ProductStatistics();
        }
        $this->statisticsObject->setTimeKey($this->timeKey);
        $this->statisticsObject->setViews($this->getFormattedData('views', 'integer'));
        $this->statisticsObject->setUniqueViews($this->getFormattedData('uniqueViews', 'integer'));
        $this->statisticsObject->setProduct($this->object);
        return $this->statisticsObject;
    }
}