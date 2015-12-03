<?php

namespace CronBundle\Import;

use ShoprenterBundle\Import\ResponseDataConverter\OrderProductDataConverter;
use ShoprenterBundle\Import\RequestModel\OrderProductRequestModel;
use ShoprenterBundle\Import\AllowanceValidator\OrderProductAllowanceValidator;
use ShoprenterBundle\Import\EntityObjectSetter\OrderProductEntityObjectSetter;
use Doctrine\Common\Collections\ArrayCollection;

class OrderProductImporter extends ShopImporter
{
    /** @var string */
    protected $importName = 'order_product';

    /** @var string */
    protected $entity = 'OrderProduct';

    /** @var OrderProductRequestModel */
    protected $requestModel;

    /** @var OrderProductDataConverter */
    protected $responseDataConverter;

    /** @var OrderProductAllowanceValidator */
    protected $allowanceValidator;

    /** @var OrderProductEntityObjectSetter */
    protected $entityObjectSetter;

    /** @var ClientAdapter */
    protected $client;

    /** @var ArrayCollection */
    protected $orderEntityCollection;

    /** @var array */
    protected $orderEntityKeyByOuterId = array();

    protected function init()
    {
        $this->initRequestModel();
        $this->initConverter();
        $this->initAllowanceValidator();
        $this->initCollections();
        $this->initEntityObjectSetter();
        $this->initOrderEntityCollection();
        $this->client->init();
    }


    protected function initRequestModel()
    {
        $this->requestModel = new OrderProductRequestModel();
    }

    protected function initConverter()
    {
        $this->responseDataConverter = new OrderProductDataConverter();
    }

    protected function initAllowanceValidator()
    {
        $this->allowanceValidator = new OrderProductAllowanceValidator();
    }

    protected function initEntityObjectSetter()
    {
        $this->entityObjectSetter = new OrderProductEntityObjectSetter();
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