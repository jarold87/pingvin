<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\CustomerImporter as MainCustomerImporter;
use CronBundle\Import\ShopImporterInterface;
use CronBundle\Import\ImporterInterface;

class CustomerImporter extends MainCustomerImporter implements ShopImporterInterface, ImporterInterface
{
    /** @var ClientAdapter */
    protected $client;

    public function import()
    {
        $this->initCollections();
        $this->client->init();
        $this->collectCustomerItems();
        $this->collectCustomers();
        $this->refreshImportLog();
        $this->createImportLog();
    }

    protected function collectCustomerItems()
    {
        if ($this->hasInProgressItemRequests()) {
            return;
        }
        $this->setCollectionLogIndex(1);
        $this->setItemLogIndex(1);
        $list = $this->getCustomers();
        $this->addItemsToProcessCollection($list);
        $this->saveCollectionItems();
    }

    protected function collectCustomers()
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
                    $data = $this->getCustomerData($value);
                    $this->setProcessed($item, $key);
                    if ($this->isAllowed($data)) {
                        $this->setCustomer(
                            array(
                                'outerId' => $data['customer_id'],
                                'lastname' => $data['lastname'],
                                'firstname' => $data['firstname'],
                                'email' => $data['email'],
                                'registrationDate' => $data['date_added'],
                                'customerGroup' => $data['customer_group'],
                                'company' => $data['company'],
                                'city' => $data['city'],
                                'country' => $data['country'],
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
    protected function getCustomers()
    {
        $sql = "
        SELECT
            c.customer_id
        FROM
            customer as c
        WHERE
            (
                c.date_added > '0000-00-00 00:00:00'
                OR c.date_modified > '0000-00-00 00:00:00'
            )
            AND c.status = 1
        ORDER BY c.customer_id ASC
        ";
        $list = $this->client->getCollectionRequest($sql);
        return $list;
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    protected function getCustomerData($id)
    {
        $sql = "
                    SELECT
                        c.customer_id,
                        c.firstname as lastname,
                        c.lastname as firstname,
                        c.email,
                        c.date_added,
                        cg.name as customer_group,
                        a.company,
                        a.city,
                        co.name as country
                    FROM
                        customer as c
                        LEFT JOIN customer_group as cg
                            ON c.customer_group_id = cg.customer_group_id
                        LEFT JOIN address as a
                            ON c.address_id = a.address_id
                        LEFT JOIN country as co
                            ON a.country_id = co.country_id
                    WHERE
                        c.customer_id = " . $id . "
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
            $item = $this->setImportItemProcess($index + 1, $value['customer_id']);
            $this->itemProcessCollection->add($item);
        }
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isAllowed($data)
    {
        if (!isset($data['customer_id'])) {
            return false;
        }
        return true;
    }
}