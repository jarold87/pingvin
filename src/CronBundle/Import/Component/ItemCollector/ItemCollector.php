<?php

namespace CronBundle\Import\Component\ItemCollector;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use CronBundle\Import\Component\ClientAdapter\ClientAdapter;
use CronBundle\Service\ImportLog;
use CronBundle\Service\RuntimeWatcher;
use CronBundle\Import\Component\RequestModel;
use CronBundle\Import\Component\ResponseDataConverter;
use CronBundle\Import\Component\AllowanceValidator;
use CronBundle\Import\Component\EntityObjectSetter;
use AppBundle\Entity\ImportItemLog;
use AppBundle\Entity\ImportItemProcess;

class ItemCollector
{
    /** @var int 1000 */
    protected $flushItemPackageNumber = 1000;

    /** @var int 10000*/
    protected $itemProcessLimit = 10000;

    /** @var string PT1H */
    protected $deadAfterTime = 'PT1H';

    /** @var EntityManager */
    protected $entityManager;

    /** @var ClientAdapter */
    protected $client;

    /** @var ImportLog */
    protected $importLog;

    /** @var RuntimeWatcher */
    protected $runtimeWatcher;

    /** @var ImportItemLog */
    protected $itemLog;

    /** @var string */
    protected $importName;

    /** @var string */
    protected $outerIdKey;

    /** @var string */
    protected $entityName;

    /** @var string */
    protected $processEntityName;

    /** @var RequestModel */
    protected $requestModel;

    /** @var ResponseDataConverter */
    protected $responseDataConverter;

    /** @var AllowanceValidator */
    protected $allowanceValidator;

    /** @var EntityObjectSetter */
    protected $entityObjectSetter;

    /** @var ArrayCollection */
    protected $itemProcessCollection;

    /** @var ArrayCollection */
    protected $existEntityCollection;

    /** @var array */
    protected $existEntityKeyByOuterId = array();

    /** @var int */
    protected $AllItemProcessCount = 0;

    /** @var int */
    protected $processedItemCount = 0;

    /** @var int */
    protected $counterToFlush = 0;

    /** @var array */
    private $error = array();

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ClientAdapter $client
     */
    public function setClient(ClientAdapter $client)
    {
        $this->client = $client;
    }

    /**
     * @param ImportLog $service
     */
    public function setImportLog(ImportLog $service)
    {
        $this->importLog = $service;
    }

    /**
     * @param $runtimeWatcher
     */
    public function setRuntimeWatcher(RuntimeWatcher $runtimeWatcher)
    {
        $this->runtimeWatcher = $runtimeWatcher;
    }

    /**
     * @param $name
     */
    public function setImportName($name)
    {
        $this->importName = $name;
    }

    /**
     * @param $outerIdKey
     */
    public function setOuterIdKey($outerIdKey)
    {
        $this->outerIdKey = $outerIdKey;
    }

    /**
     * @param $entity
     */
    public function setEntityName($entity)
    {
        $this->entityName = $entity;
    }

    /**
     * @param RequestModel $requestModel
     */
    public function setRequestModel(RequestModel $requestModel)
    {
        $this->requestModel = $requestModel;
    }

    /**
     * @param ResponseDataConverter $responseDataConverter
     */
    public function setResponseDataConverter(ResponseDataConverter $responseDataConverter)
    {
        $this->responseDataConverter = $responseDataConverter;
    }

    /**
     * @param AllowanceValidator $allowanceValidator
     */
    public function setAllowanceValidator(AllowanceValidator $allowanceValidator)
    {
        $this->allowanceValidator = $allowanceValidator;
    }

    /**
     * @param EntityObjectSetter $entityObjectSetter
     */
    public function setEntityObjectSetter(EntityObjectSetter $entityObjectSetter)
    {
        $this->entityObjectSetter = $entityObjectSetter;
    }

    /**
     * @return array|bool
     */
    public function getError()
    {
        if ($this->error) {
            return $this->error;
        }
        return false;
    }

    /**
     * @return ImportItemLog
     */
    public function getLastItemIndex()
    {
        return $this->itemLog->getLastIndex();
    }

    /**
     * @return int
     */
    public function getUnProcessItemCount()
    {
        if (!$this->itemProcessCollection) {
            return 0;
        }
        return $this->itemProcessCollection->count();
    }

    public function init()
    {
        $this->loadImportItemLog();
    }

    public function collect()
    {
        $this->entityManager->flush();
    }


    public function initExistEntityCollection()
    {
        $this->existEntityCollection = new ArrayCollection();
        $this->loadExistEntityCollection();
    }

    public function initItemProcessCollection()
    {
        $this->itemProcessCollection = new ArrayCollection();
        $this->loadItemsToProcessCollection();
    }

    public function collectDeadItem()
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
            // TODO warning rögzítése
            return;
        }
        foreach ($deadEntities as $entity) {
            $entity->setIsDead(1);
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * @return bool
     */
    public function isFinishedItemCollect()
    {
        if ($this->AllItemProcessCount > $this->processedItemCount) {
            return false;
        }
        return true;
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

    public function setItemLogFinish()
    {
        $this->itemLog->setFinishDate(new \DateTime());
        $this->entityManager->persist($this->itemLog);
    }

    /**
     * @return null|object
     */
    protected function loadImportItemLog()
    {
        $log = $this->entityManager->getRepository('AppBundle:ImportItemLog')->findOneBy(
            array('importName' => $this->importName, 'finishDate' => null)
        );
        if (!$log) {
            $log = new ImportItemLog();
            $log->setImportName($this->importName);
            $log->setLastIndex(1);
            $log->setFinishDate(new \DateTime('0000-00-00'));
        }
        $this->itemLog = $log;
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

    /**
     * @param $error
     */
    protected function addError($error)
    {
        $this->error[] = $error;
    }

    /**
     * @return bool
     */
    protected function isInLimits()
    {
        if ($this->isTimeOut()) {
            return false;
        }
        if ($this->getError()) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function isTimeOut()
    {
        return $this->runtimeWatcher->isTimeOut();
    }

    protected function loadItemsToProcessCollection()
    {
        $items = $this->entityManager->getRepository('AppBundle:' . $this->processEntityName)->findAll();
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
        $objects = $this->entityManager->getRepository('AppBundle:' . $this->entityName)->findAll();
        if ($objects) {
            $key = 0;
            foreach ($objects as $object) {
                $this->existEntityCollection->add($object);
                $this->existEntityKeyByOuterId[$object->getOuterId()] = $key;
                $key++;
            }
        }
    }

    protected function manageFlush()
    {
        if ($this->counterToFlush >= $this->flushItemPackageNumber) {
            $this->entityManager->flush();
            $this->counterToFlush = 0;
        }
        $this->counterToFlush++;
    }


    /**
     * TODO ItemCollectorban is benne van. Service???
     * @param $index
     */
    protected function setItemLogIndex($index)
    {
        $this->itemLog->setLastIndex($index);
        $this->entityManager->persist($this->itemLog);
    }

    /**
     * @param ImportItemProcess $item
     * @return string
     */
    protected function getImportProcessIndex(ImportItemProcess $item)
    {
        return $item->getItemIndex();
    }

    /**
     * @param ImportItemProcess $item
     * @return string
     */
    protected function getImportProcessValue(ImportItemProcess $item)
    {
        return $item->getItemValue();
    }
}