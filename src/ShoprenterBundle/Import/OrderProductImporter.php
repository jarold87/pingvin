<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\OrderProductImporter as MainOrderProductImporter;
use CronBundle\Import\ShopImporterInterface;
use CronBundle\Import\ImporterInterface;

class OrderProductImporter extends MainOrderProductImporter implements ShopImporterInterface, ImporterInterface
{
    /** @var ClientAdapter */
    protected $client;

    public function import()
    {
        $this->initCollections();
        $this->client->init();
        $this->collectOrderProductItems();
        $this->collectOrderProducts();
        $this->entityManager->flush();
        $this->createImportLog();
        $this->entityManager->flush();
    }

    protected function collectOrderProductItems()
    {
        if ($this->hasInProgressItemRequests()) {
            return;
        }
        $this->setCollectionLogIndex(1);
        $this->setItemLogIndex(1);
        $list = $this->getOrderProducts();
        $this->addItemsToProcessCollection($list);
        $this->saveItemsToProcess();
        $this->setCollectionLogFinish();
    }

    protected function collectOrderProducts()
    {
        if ($this->itemProcessCollection->count()) {
            $items = $this->itemProcessCollection->toArray();
            foreach ($items as $key => $item) {
                $index = $item->getItemIndex();
                $value = $item->getItemValue();
                $this->actualTime = round(microtime(true) - $this->startTime);
                if ($this->actualTime < $this->timeLimit) {
                    $this->setItemLogIndex($index);
                    $data = $this->getOrderProductData($value);
                    $this->setProcessed($item, $key);
                    if ($this->isAllowed($data)) {
                        $this->setOrderProduct(
                            array(
                                'outerId' => $data['order_product_id'],
                                'orderOuterId' => $data['order_id'],
                                'productOuterId' => $data['product_id'],
                                'quantity' => $data['quantity'],
                                'total' => $data['total'],
                            )
                        );
                    }
                } else {
                    $this->timeOut = 1;
                    break;
                }
            }
        }
        if ($this->timeOut == 0) {
            $this->setItemLogFinish();
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getOrderProducts()
    {
        $sql = "
        SELECT
            op.order_product_id
        FROM
            `order` as o
            LEFT JOIN order_product as op
                ON o.order_id = op.order_id
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
    protected function getOrderProductData($id)
    {
        $sql = "
                    SELECT
                        op.order_product_id,
                        op.order_id,
                        op.product_id,
                        op.quantity,
                        op.total
                    FROM
                        order_product as op
                    WHERE
                        op.order_product_id = " . $id . "
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
            $item = $this->setImportItemProcess($index + 1, $value['order_product_id']);
            $this->itemProcessCollection->add($item);
        }
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isAllowed($data)
    {
        if (!isset($data['order_product_id'])) {
            return false;
        }
        return true;
    }
}