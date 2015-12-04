<?php

namespace CronBundle\Import\ItemCollector;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use CronBundle\Import\ClientAdapter;
use CronBundle\Service\ImportLog;
use CronBundle\Import\RequestModel;
use CronBundle\Import\ResponseDataConverter;
use CronBundle\Import\AllowanceValidator;
use CronBundle\Import\EntityObjectSetter;
use AppBundle\Entity\ImportItemLog;

class ItemCollector
{
    /** @var int 1000 */
    protected $flushItemPackageNumber = 1000;

    /** @var int 10000*/
    protected $itemProcessLimit = 10000;

    /** @var string PT1H */
    protected $deadAfterTime = 'PT5M';

    /** @var EntityManager */
    protected $entityManager;

    /** @var ClientAdapter */
    protected $client;

    /** @var ImportItemLog */
    protected $itemLog;

    /** @var ImportLog */
    protected $importLog;

    /** @var string */
    protected $importName;

    /** @var string */
    protected $outerIdKey;

    /** @var string */
    protected $entityName;

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

    /** @var float */
    protected $runtime = 0.00;

    /** @var */
    protected $startTime;

    /** @var */
    protected $timeLimit;

    /** @var int */
    protected $timeOut = 0;

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
     * @param ImportItemLog $log
     */
    public function setItemLog(ImportItemLog $log)
    {
        $this->itemLog = $log;
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
     * @param $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @param $runtime
     */
    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;
    }

    /**
     * @param $timeLimit
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
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
     * @return int
     */
    public function getUnProcessItemCount()
    {
        return $this->itemProcessCollection->count();
    }

    public function collect()
    {

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
            return;
        }
        foreach ($deadEntities as $entity) {
            $entity->setIsDead(1);
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
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
        if ($this->timeOut == 1) {
            return false;
        }
        $this->refreshRunTime();
        if ($this->runtime >= $this->timeLimit) {
            return false;
        }
        return true;
    }

    protected function refreshRunTime()
    {
        $this->runtime = round(microtime(true) - $this->startTime, 2);
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
     * @param $index
     */
    protected function setItemLogIndex($index)
    {
        if ($this->itemLog) {
            $this->itemLog->setLastIndex($index);
            return;
        }
        $log = new ImportItemLog();
        $log->setImportName($this->importName);
        $log->setLastIndex($index);
        $log->setFinishDate(new \DateTime('0000-00-00'));
        $this->entityManager->persist($log);
        $this->itemLog = $log;
    }
}