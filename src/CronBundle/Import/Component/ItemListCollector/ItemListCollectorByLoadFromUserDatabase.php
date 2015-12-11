<?php

namespace CronBundle\Import\Component\ItemListCollector;

class ItemListCollectorByLoadFromUserDatabase extends ItemListCollector
{
    /** @var string */
    protected $processEntityName;

    /** @var string */
    protected $sourceEntityName;

    /**
     * @param $name
     */
    public function setSourceEntityName($name)
    {
        $this->sourceEntityName = $name;
    }

    public function init()
    {
        parent::init();
    }

    public function collect()
    {
        if ($this->isInProgressItemProcess()) {
            return;
        }
        $this->setCollectionLogIndex(1);
        $list = $this->loadSourceEntities();
        $this->addToProcessCollection($list);
        $this->saveItemsToProcess();
        $this->setItemLogIndex(1);
        $this->setCollectionLogFinish();
        parent::collect();
    }

    protected function loadSourceEntities()
    {
        $list = array();
        $products = $this->entityManager->getRepository('AppBundle:Product')->findAll();
        if ($products) {
            foreach ($products as $product) {
                $get = 'get' . $this->outerIdKey;
                $list[][$this->outerIdKey] = $product->$get();
            }
        }
        return $list;
    }
}