<?php

namespace CronBundle\Import\ItemCollector;

use AppBundle\Entity\ImportItemProcess;
use Doctrine\Common\Collections\ArrayCollection;

class PackageItemCollector extends ItemCollector
{
    public function collect()
    {
        $this->init();
        if (!$this->itemProcessCollection->count()) {
            return;
        }
        $items = $this->itemProcessCollection->toArray();
        $this->collectItemsByPackageRequests($items);
    }

    public function collectDeadItem()
    {
        $this->init();
        parent::collectDeadItem();
    }

    protected function init()
    {
        $this->existEntityCollection = new ArrayCollection();
        $this->itemProcessCollection = new ArrayCollection();
        $this->loadItemsToProcessCollection();
        $this->loadExistEntityCollection();
    }

    /**
     * @param $items
     */
    protected function collectItemsByPackageRequests($items)
    {
        $package = array();
        foreach ($items as $index => $values) {
            if (!$this->isInLimits()) {
                $this->timeOut = 1;
                break;
            }
            $package[$index] = $values;
            if (count($package) < $this->flushItemPackageNumber && count($package) < count($items)) {
                continue;
            }
            $this->processItemPackage($package);
            $package = array();
        }
    }

    protected function processItemPackage(array $package)
    {
        $itemValues = array();
        $itemProcessStatus = array();
        foreach ($package as $item) {
            $itemValues[] = $item->getItemValue();
            $itemProcessStatus[$item->getItemValue()] = 0;
        }
        $request = $this->requestModel->getItemPackageRequest($itemValues);
        $list = $this->client->getPackageRequest($request);
        if ($this->client->getError()) {
            $this->addError($this->client->getError());
            return;
        }
        foreach ($list as $responseData) {
            $this->responseDataConverter->setOuterId($responseData[$this->outerIdKey]);
            $this->responseDataConverter->setResponseData($responseData);
            $data = $this->responseDataConverter->getConvertedData();
            if (isset($itemProcessStatus[$data['outerId']])) {
                $itemProcessStatus[$data['outerId']] = 1;
            }
            if (!$this->isAllowed($data)) {
                $this->importLog->addNotAllowed($this->importName, $data['outerId']);
                continue;
            }
            $this->setEntity($data);
        }
        foreach ($package as $key => $item) {
            $this->setProcessed($item, $key);
            if (!$itemProcessStatus[$item->getItemValue()]) {
                $this->importLog->addEmptyResponse($this->importName, $item->getItemValue());
            }
        }
        $this->manageFlush();
    }

    /**
     * @param $data
     */
    protected function setEntity($data)
    {
        $this->validateOuterIdInData($data);
        $outerId = $data['outerId'];
        $object = $this->getEntityObject($outerId);
        $object = $this->setDataToObject($object, $data);
        $this->entityManager->persist($object);
    }

    /**
     * @param $object
     * @param $data
     * @return \AppBundle\Entity\Product
     */
    protected function setDataToObject($object, $data)
    {
        $this->entityObjectSetter->setObject($object);
        $this->entityObjectSetter->setData($data);
        return $this->entityObjectSetter->getObject();
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isAllowed($data)
    {
        $this->allowanceValidator->setData($data);
        if ($this->allowanceValidator->isAllowed()) {
            return true;
        }
        return false;
    }

    /**
     * @param $data
     * @throws \Exception
     */
    protected function validateOuterIdInData($data)
    {
        if (!isset($data['outerId']) || !$data['outerId']) {
            throw new \Exception("Not a valid key or not exist in data!");
        }
    }


    /**
     * @param $outerId
     * @return mixed
     */
    protected function getEntityObject($outerId)
    {
        if ($this->isExistEntityObject($outerId)) {
            return $this->getObjectFromExistEntityCollectionByOuterId($outerId);
        }
        return $this->newEntityObject($outerId);
    }

    /**
     * @param $outerId
     * @return mixed
     */
    protected function newEntityObject($outerId)
    {
        $className = 'AppBundle\\Entity\\' . $this->entityName;
        $object = new $className();
        $object->setOuterId($outerId);
        return $object;
    }



    /**
     * @param $outerId
     * @return bool
     */
    protected function isExistEntityObject($outerId)
    {
        if (isset($this->existEntityKeyByOuterId[$outerId])) {
            return true;
        }
        return false;
    }

    /**
     * @param $outerId
     * @return mixed
     */
    protected function getObjectFromExistEntityCollectionByOuterId($outerId)
    {
        return $this->existEntityCollection->get(
            $this->existEntityKeyByOuterId[$outerId]
        );
    }


    /**
     * @param $item
     * @param $key
     */
    protected function setProcessed(ImportItemProcess $item, $key)
    {
        $this->entityManager->remove($item);
        $this->itemProcessCollection->remove($key);
        $this->importLog->addProcessItemCount();
        $this->processedItemCount++;
        $index = $item->getItemIndex();
        $this->setItemLogIndex($index);
    }
}