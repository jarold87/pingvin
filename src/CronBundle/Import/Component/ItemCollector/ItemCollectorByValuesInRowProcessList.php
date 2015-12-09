<?php

namespace CronBundle\Import\Component\ItemCollector;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\ImportRowProcess;

class ItemCollectorByValuesInRowProcessList extends ItemCollector
{
    /** @var ArrayCollection */
    protected $entityCollection;

    /** @var array */
    protected $entityKeyByRowKey = array();

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
                $this->setProcessedRow($item, $key);
                continue;
            }
            $this->responseDataConverter->setOuterId($item->getRowKey());
            $this->responseDataConverter->setResponseData($item);
            $data = $this->responseDataConverter->getConvertedData();
            $this->setStatisticsEntity($entity, $data);
            $this->setProcessedRow($item, $key);
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
                $rowKeyValue = $object->$get();
                if ($rowKeyValue) {
                    $this->entityCollection->add($object);
                    $this->entityKeyByRowKey[md5('/' . $rowKeyValue)] = $key;
                }
                $key++;
            }
        }
    }

    /**
     * @param ImportRowProcess $item
     * @return bool|mixed|null
     */
    protected function searchEntity(ImportRowProcess $item)
    {
        $rowKey = $item->getRowKey();
        if (!isset($this->entityKeyByRowKey[md5($rowKey)])) {
            return false;
        }
        return $this->entityCollection->get(
            $this->entityKeyByRowKey[md5($rowKey)]
        );
    }

    /**
     * @param $item
     * @param $key
     */
    protected function setProcessedRow(ImportRowProcess $item, $key)
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