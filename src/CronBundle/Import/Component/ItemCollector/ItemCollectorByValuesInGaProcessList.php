<?php

namespace CronBundle\Import\Component\ItemCollector;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\ImportGaRowProcess;

class ItemCollectorByValuesInGaProcessList extends ItemCollector
{
    /** @var ArrayCollection */
    protected $entityCollection;

    /** @var array */
    protected $entityKeyByDimensionKey = array();

    public function init()
    {
        $this->initEntityCollection();
        $this->initExistEntityCollection();
        $this->initItemProcessCollection();
        parent::init();
    }

    public function collect()
    {
        if (!$this->itemProcessCollection->count()) {
            return;
        }
        $items = $this->itemProcessCollection->toArray();
        $this->collectItemsByValuesInProcessList($items);
        parent::collect();
    }

    /**
     * @param $items
     */
    protected function collectItemsByValuesInProcessList($items)
    {
        foreach ($items as $key => $item) {
            if (!$this->isInLimits()) {
                break;
            }
            $entity = $this->searchEntity($item);
            if (!$entity) {
                $this->setProcessedGaRow($item, $key);
                continue;
            }
            $this->responseDataConverter->setOuterId($item->getDimensionKey());
            $this->responseDataConverter->setResponseData($item);
            $data = $this->responseDataConverter->getConvertedData();
            $this->setStatisticsEntity($entity, $data);
            $this->setProcessedGaRow($item, $key);
            $this->manageFlush();
        }
    }

    protected function initEntityCollection()
    {
        $this->entityCollection = new ArrayCollection();
        $this->loadEntityCollection();
    }

    protected function loadEntityCollection()
    {
        $objects = $this->entityManager->getRepository('AppBundle:' . $this->entityName)->findAll();
        if ($objects) {
            $key = 0;
            foreach ($objects as $object) {
                $get = 'get' . $this->outerIdKey;
                $dimensionKeyValue = $object->$get();
                if ($dimensionKeyValue) {
                    $this->entityCollection->add($object);
                    $this->entityKeyByDimensionKey[md5('/' . $dimensionKeyValue)] = $key;
                }
                $key++;
            }
        }
    }

    /**
     * @param ImportGaRowProcess $item
     * @return bool|mixed|null
     */
    protected function searchEntity(ImportGaRowProcess $item)
    {
        $dimensionKey = $item->getDimensionKey();
        if (!isset($this->entityKeyByDimensionKey[md5($dimensionKey)])) {
            return false;
        }
        return $this->entityCollection->get(
            $this->entityKeyByDimensionKey[md5($dimensionKey)]
        );
    }

    /**
     * @param $item
     * @param $key
     */
    protected function setProcessedGaRow(ImportGaRowProcess $item, $key)
    {
        $this->entityManager->remove($item);
        $this->itemProcessCollection->remove($key);
        $this->importLog->addProcessItemCount();
        $this->processedItemCount++;
        $this->setItemLogIndex($item->getRowIndex());
    }

    /**
     * @param $object
     * @param $data
     */
    protected function setStatisticsEntity($object, $data)
    {
        $this->entityObjectSetter->setObject($object);
        $this->entityObjectSetter->setData($data);
        $object = $this->entityObjectSetter->getObject();
        $this->entityManager->persist($object);
    }
}