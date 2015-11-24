<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\OrderImporter as MainOrderImporter;
use CronBundle\Import\ImporterInterface;

class OrderImporter extends MainOrderImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'order_id';

    /** @var ClientAdapter */
    protected $client;

    public function import()
    {
        $this->initCollections();
        $this->client->init();
        $this->collectOrderItems();
        $this->collectOrders();
        $this->saveImportLog();
    }

    protected function collectOrderItems()
    {
        if ($this->hasInProgressItemRequests()) {
            return;
        }
        $this->setCollectionLogIndex(1);
        $this->setItemLogIndex(1);
        $list = $this->getOrders();
        $this->addItemsToProcessCollection($list);
        $this->saveCollectionItems();
    }

    protected function collectOrders()
    {
        if (!$this->isInLimits()) {
            $this->timeOut = 1;
            return;
        }
        $this->loadItemsToProcessCollection();
        $this->loadExistEntityCollection();
        if ($this->itemProcessCollection->count()) {
            $items = $this->itemProcessCollection->toArray();
            $counterToFlush = 0;
            foreach ($items as $key => $item) {
                $index = $item->getItemIndex();
                $value = $item->getItemValue();
                if ($this->isInLimits()) {
                    $this->setItemLogIndex($index);
                    $data = $this->getOrderData($value);
                    $this->setProcessed($item, $key);
                    if ($this->isAllowed($data)) {
                        $this->setOrder(
                            array(
                                'outerId' => $data['order_id'],
                                'customerOuterId' => $data['customer_id'],
                                'shippingMethod' => $data['shipping_method'],
                                'paymentMethod' => $data['payment_method'],
                                'currency' => $data['currency'],
                                'orderDate' => $data['date_added'],
                            )
                        );
                    }
                    if ($counterToFlush == $this->flushItemPackageNumber) {
                        $this->entityManager->flush();
                        $counterToFlush = 0;
                    } else {
                        $counterToFlush++;
                    }
                } else {
                    $this->timeOut = 1;
                    break;
                }
            }
        }
        if ($this->isFinishedImport()) {
            $this->setItemLogFinish();
        }
        $this->entityManager->flush();
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getOrders()
    {
        $sql = "
        SELECT
            o.order_id
        FROM
            `order` as o
        WHERE
            (
                o.date_added > '0000-00-00 00:00:00'
                OR o.date_modified > '0000-00-00 00:00:00'
            )
            AND o.order_status_id != 0
        ORDER BY o.order_id ASC
        ";
        $list = $this->client->getCollectionRequest($sql);
        return $list;
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    protected function getOrderData($id)
    {
        $sql = "
                    SELECT
                        o.order_id,
                        o.customer_id,
                        o.shipping_method,
                        o.payment_method,
                        o.currency,
                        o.date_added
                    FROM
                        `order` as o
                    WHERE
                        o.order_id = " . $id . "
                    LIMIT 0,1
        ";

        $data = $this->client->getRequest($sql);
        return $data;
    }

    /**
     * @param $items
     */
    protected function addItemsToProcessCollection($items)
    {
        if (!$items) {
            return;
        }
        foreach ($items as $index => $value) {
            $item = $this->setImportItemProcess($index + 1, $value['order_id']);
            $this->itemProcessCollection->add($item);
        }
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isAllowed($data)
    {
        if (!isset($data['order_id'])) {
            return false;
        }
        return true;
    }
}