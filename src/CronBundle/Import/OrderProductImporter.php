<?php

namespace CronBundle\Import;

use ShoprenterBundle\Import\Component\EntityObjectSetter\OrderProductEntityObjectSetter;
use Doctrine\Common\Collections\ArrayCollection;

class OrderProductImporter extends ShopImporter
{
    /** @var string */
    protected $entityName = 'OrderProduct';

    /** @var OrderProductEntityObjectSetter */
    protected $entityObjectSetter;

    /** @var ArrayCollection */
    protected $orderEntityCollection;

    /** @var array */
    protected $orderEntityKeyByOuterId = array();

    public function init()
    {
        parent::init();
        $this->initOrderEntityCollection();
    }

    protected function initOrderEntityCollection()
    {
        $this->orderEntityCollection = new ArrayCollection();
        $objects = $this->entityManager->getRepository('AppBundle:Order')->findAll();
        if ($objects) {
            $key = 0;
            foreach ($objects as $object) {
                $this->orderEntityCollection->add($object);
                $this->orderEntityKeyByOuterId[$object->getOuterId()] = $key;
                $key++;
            }
        }
        $this->entityObjectSetter->setOrderEntityCollection($this->orderEntityCollection);
        $this->entityObjectSetter->setOrderEntityKeyByOuterId($this->orderEntityKeyByOuterId);
    }
}