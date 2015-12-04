<?php

namespace GoogleApiBundle\Import;

use CronBundle\Import\Importer;
use CronBundle\Import\RequestModel;
use CronBundle\Import\ResponseDataConverter;
use CronBundle\Import\EntityObjectSetter;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\ImportGaRowProcess;

class AnalyticsImporter extends Importer
{
    /** @var string */
    protected $importName = 'product_page_view';

    /** @var string */
    protected $entity = 'Product';

    /** @var RequestModel */
    protected $requestModel;

    /** @var ResponseDataConverter */
    protected $responseDataConverter;

    /** @var EntityObjectSetter */
    protected $entityObjectSetter;

    /** @var ClientAdapter */
    protected $client;

    /** @var ArrayCollection */
    protected $rowProcessCollection;

    /** @var ArrayCollection */
    protected $entityCollection;

    /** @var string */
    protected $dimensionKey;

    /** @var int */
    protected $AllItemProcessCount = 0;

    /** @var int */
    protected $processedItemCount = 0;

    /** @var array */
    protected $entityKeyByDimensionKey = array();

    protected function init()
    {
        $this->initCollections();
        $this->client->init();
    }

    protected function initCollections()
    {
        $this->rowProcessCollection = new ArrayCollection();
        $this->entityCollection = new ArrayCollection();
    }

    protected function collectItems()
    {
        if ($this->hasInProcessItemRequests()) {
            return;
        }
        $this->setCollectionLogIndex(1);
        $request = $this->requestModel->getCollectionRequest();
        $listObject = $this->client->getCollectionRequest($request);
        if ($this->client->getError()) {
            $this->addError($this->client->getError());
            return;
        }
        $this->addRowsToProcessCollection($listObject);
        $this->saveRowsToProcess();
        $this->setItemLogIndex(1);
        $this->setCollectionLogFinish();
        $this->entityManager->flush();
        $this->clearRowsToProcessCollection();
    }

    protected function collectItemData()
    {
        if (!$this->isInLimits()) {
            $this->timeOut = 1;
            return;
        }
        $this->loadRowsToProcessCollection();
        if (!$this->rowProcessCollection->count()) {
            return;
        }
        $this->loadEntityCollection();
        $items = $this->rowProcessCollection->toArray();
        foreach ($items as $key => $item) {
            if (!$this->isInLimits()) {
                $this->timeOut = 1;
                break;
            }
            $entity = $this->searchEntity($item);
            if (!$entity) {
                $this->setProcessed($item, $key);
                continue;
            }
            $this->responseDataConverter->setResponseData($item);
            $data = $this->responseDataConverter->getConvertedData();
            $this->setEntity($entity, $data);
            $this->setProcessed($item, $key);
            $this->manageFlush();
        }
        if ($this->isFinishedImport()) {
            $this->setItemLogFinish();
        }
        $this->entityManager->flush();
    }

    /**
     * @param $listObject
     */
    protected function addRowsToProcessCollection($listObject)
    {
        if (!$listObject->getRows()) {
            return;
        }
        foreach ($listObject->getRows() as $index => $values) {
            $dimensionKey = $values[0];
            unset($values[0]);
            $valuesString = serialize($values);
            $item = $this->setImportGaRowProcess($index + 1, $dimensionKey, $valuesString);
            $this->rowProcessCollection->add($item);
        }
    }

    /**
     * @param $index
     * @param $dimensionKey
     * @param $valuesString
     * @return ImportGaRowProcess
     */
    protected function setImportGaRowProcess($index, $dimensionKey, $valuesString)
    {
        $row = new ImportGaRowProcess();
        $row->setRowIndex($index);
        $row->setDimensionKey($dimensionKey);
        $row->setRowValues($valuesString);
        return $row;
    }

    protected function saveRowsToProcess()
    {
        $items = $this->rowProcessCollection->toArray();
        if (!$items) {
            return;
        }
        foreach ($items as $item) {
            $this->entityManager->persist($item);
        }
    }

    protected function clearRowsToProcessCollection()
    {
        $this->rowProcessCollection->clear();
    }

    /**
     * @param $object
     * @param $data
     */
    protected function setEntity($object, $data)
    {
        $this->entityObjectSetter->setObject($object);
        $this->entityObjectSetter->setData($data);
        $object = $this->entityObjectSetter->getObject();
        $this->entityManager->persist($object);
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
    protected function setProcessed(ImportGaRowProcess $item, $key)
    {
        $this->entityManager->remove($item);
        $this->rowProcessCollection->remove($key);
        $this->importLog->addProcessItemCount();
        $this->processedItemCount++;
        $this->setItemLogIndex($item->getRowIndex());
    }

    protected function loadRowsToProcessCollection()
    {
        $items = $this->entityManager->getRepository('AppBundle:ImportGaRowProcess')->findAll();
        $this->AllItemProcessCount = count($items);
        if ($items) {
            $limitCounter = 0;
            foreach ($items as $item) {
                if ($limitCounter > $this->itemProcessLimit) {
                    break;
                }
                $this->rowProcessCollection->add($item);
                $limitCounter++;
            }
        }
    }

    protected function loadEntityCollection()
    {
        $objects = $this->entityManager->getRepository('AppBundle:' . $this->entity)->findAll();
        if ($objects) {
            $key = 0;
            foreach ($objects as $object) {
                $get = 'get' . $this->dimensionKey;
                $dimensionKeyValue = $object->$get();
                if ($dimensionKeyValue) {
                    $this->entityCollection->add($object);
                    $this->entityKeyByDimensionKey[md5('/' . $dimensionKeyValue)] = $key;
                }
                $key++;
            }
        }
    }

}