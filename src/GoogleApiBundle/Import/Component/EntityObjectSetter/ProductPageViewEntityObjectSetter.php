<?php

namespace GoogleApiBundle\Import\Component\EntityObjectSetter;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductStatistics;

class ProductPageViewEntityObjectSetter extends GaEntityObjectSetter
{
    /** @var Product */
    protected $object;

    /** @var ProductStatistics */
    protected $statisticsObject;

    /**
     * @return Product
     */
    public function setDataToObject()
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
        $this->entityManager->persist($this->statisticsObject);
        parent::setDataToObject();
    }
}