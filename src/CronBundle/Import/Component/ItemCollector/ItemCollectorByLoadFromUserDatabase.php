<?php

namespace CronBundle\Import\Component\ItemCollector;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\ImportItemProcess;

class ItemCollectorByLoadFromUserDatabase extends ItemCollector
{
    /** @var string */
    protected $sourceEntityName;

    /** @var ArrayCollection */
    protected $entityCollection;

    /** @var array */
    protected $entityKeyByOuterId = array();

    /**
     * @param $name
     */
    public function setSourceEntityName($name)
    {
        $this->sourceEntityName = $name;
    }

    public function init()
    {
        $this->initEntityCollection();
        $this->initItemProcessCollection();
        parent::init();
    }

    protected function initEntityCollection()
    {
        $this->entityCollection = new ArrayCollection();
        $this->loadEntityCollection();
    }

    protected function loadEntityCollection()
    {
        $objects = $this->entityManager->getRepository('AppBundle:' . $this->sourceEntityName)->findAll();
        if ($objects) {
            $key = 0;
            foreach ($objects as $object) {
                $get = 'get' . $this->outerIdKey;
                $outerId = $object->$get();
                $this->entityCollection->add($object);
                $this->entityKeyByOuterId[$outerId] = $key;
                $key++;
            }
        }
    }

    /**
     * @param ImportItemProcess $item
     * @return bool|mixed|null
     */
    protected function searchEntity(ImportItemProcess $item)
    {
        $outerId = $item->getItemValue();
        if (!isset($this->entityKeyByOuterId[$outerId])) {
            return false;
        }
        return $this->entityCollection->get(
            $this->entityKeyByOuterId[$outerId]
        );
    }
}