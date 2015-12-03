<?php

namespace CronBundle\Import;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\ImportItemProcess;

class ShopImporter extends Importer
{
    /** @var string PT1H */
    protected $deadAfterTime = 'PT5M';

    /** @var string */
    protected $entity;

    /** @var string */
    protected $outerIdKey;

    /** @var ArrayCollection */
    protected $itemProcessCollection;

    /** @var ArrayCollection */
    protected $existEntityCollection;

    /** @var RequestModel */
    protected $requestModel;

    /** @var ResponseDataConverter */
    protected $responseDataConverter;

    /** @var AllowanceValidator */
    protected $allowanceValidator;

    /** @var EntityObjectSetter */
    protected $entityObjectSetter;

    /** @var array */
    protected $existEntityKeyByOuterId = array();

    /** @var int */
    protected $AllItemProcessCount = 0;

    /** @var int */
    protected $processedItemCount = 0;

    protected function initCollections()
    {
        $this->itemProcessCollection = new ArrayCollection();
        $this->existEntityCollection = new ArrayCollection();
    }

    protected function collectItemData()
    {
        if (!$this->isInLimits()) {
            $this->timeOut = 1;
            return;
        }
        $this->loadItemsToProcessCollection();
        $this->loadExistEntityCollection();
        if (!$this->itemProcessCollection->count()) {
            return;
        }
        $items = $this->itemProcessCollection->toArray();
        $this->collectByItems($items);
        $this->entityManager->flush();
    }

    protected function collectDeadItem()
    {
        $deadEntities = array();
        $entities = $this->existEntityCollection->toArray();
        foreach ($entities as $entity) {
            if ($this->isDeadItem($entity)) {
                $deadEntities[] = $entity;
            }
        }
        if (!$deadEntities) {
            return;
        }
        if ($this->isAllDead($deadEntities)) {
            // Biztonsági okokból nem csinálunk semmitsem akkor,
            // ha az összes entitásra igaz
            return;
        }
        foreach ($deadEntities as $entity) {
            $entity->setIsDead(1);
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * @param $items
     */
    protected function collectByItems($items)
    {
        foreach ($items as $key => $item) {
            if (!$this->isInLimits()) {
                $this->timeOut = 1;
                break;
            }
            $responseData = $this->getItemData($item);
            if (!$responseData) {
                $this->addError($this->importName . ' -> not response item data (' . $key . ')');
                return;
            }
            $this->responseDataConverter->setResponseData($responseData);
            $data = $this->responseDataConverter->getConvertedData();
            $this->setProcessed($item, $key);
            if (!$this->isAllowed($data)) {
                $this->importLog->addNotAllowed($this->importName, $item->getItemValue());
                continue;
            }
            $this->setEntity($data);
            $this->manageFlush();
        }
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
     * @param ImportItemProcess $item
     * @return mixed
     */
    protected function getItemData(ImportItemProcess $item)
    {
        $index = $item->getItemIndex();
        $key = $item->getItemValue();
        $this->setItemLogIndex($index);
        $request = $this->requestModel->getItemRequest($key);
        return $this->client->getRequest($request);
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
     * @param $entity
     * @return bool
     */
    protected function isDeadItem($entity)
    {
        /** @var \DateTime $updateDate */
        $updateDate = $entity->getUpdateDate();
        $now = new \DateTime();
        $calc = $updateDate;
        $calc->add(new \DateInterval($this->deadAfterTime));
        if ($calc < $now) {
            return true;
        }
        return false;
    }

    /**
     * @param array $deadEntities
     * @return bool
     */
    protected function isAllDead(array $deadEntities)
    {
        if (count($deadEntities) == $this->existEntityCollection->count()) {
            return true;
        }
        return false;
    }

    protected function saveImportLog()
    {
        $this->importLog->setUserLastIndex($this->getLastItemIndexFromLog());
        $this->importLog->setUnProcessItemCount($this->itemProcessCollection->count());
        parent::saveImportLog();
    }

    /**
     * @param $items
     * @throws \Exception
     */
    protected function addItemsToProcessCollection($items)
    {
        if (!$items) {
            return;
        }
        if (!$this->outerIdKey) {
            throw new \Exception("Not a valid outerIdKey!");
        }
        foreach ($items as $index => $value) {
            $item = $this->setImportItemProcess($index + 1, $value[$this->outerIdKey]);
            $this->itemProcessCollection->add($item);
        }
    }

    protected function loadItemsToProcessCollection()
    {
        $items = $this->entityManager->getRepository('AppBundle:ImportItemProcess')->findAll();
        $this->AllItemProcessCount = count($items);
        if ($items) {
            $limitCounter = 0;
            foreach ($items as $item) {
                if ($limitCounter > $this->itemProcessLimit) {
                    break;
                }
                $this->itemProcessCollection->add($item);
                $limitCounter++;
            }
        }
    }

    protected function loadExistEntityCollection()
    {
        $objects = $this->entityManager->getRepository('AppBundle:' . $this->entity)->findAll();
        if ($objects) {
            $key = 0;
            foreach ($objects as $object) {
                $this->existEntityCollection->add($object);
                $this->existEntityKeyByOuterId[$object->getOuterId()] = $key;
                $key++;
            }
        }
    }

    protected function saveItemsToProcess()
    {
        $items = $this->itemProcessCollection->toArray();
        if (!$items) {
            return;
        }
        foreach ($items as $item) {
            $this->entityManager->persist($item);
        }
    }

    protected function clearItemsToProcessCollection()
    {
        $this->itemProcessCollection->clear();
    }

    /**
     * @param $index
     * @param $value
     * @return ImportItemProcess
     */
    protected function setImportItemProcess($index, $value)
    {
        $item = new ImportItemProcess();
        $item->setItemIndex($index);
        $item->setItemValue($value);
        return $item;
    }

    /**
     * @param $item
     * @param $key
     */
    protected function setProcessed($item, $key)
    {
        $this->entityManager->remove($item);
        $this->itemProcessCollection->remove($key);
        $this->importLog->addProcessItemCount();
        $this->processedItemCount++;
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
        $className = 'AppBundle\\Entity\\' . $this->entity;
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
     * @return bool
     */
    public function isFinishedImport()
    {
        if ($this->AllItemProcessCount > $this->processedItemCount) {
            return false;
        }
        return parent::isFinishedImport();
    }
}