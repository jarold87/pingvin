<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\ProductImporter as MainProductImporter;
use CronBundle\Import\ShopImporterInterface;
use CronBundle\Import\ImporterInterface;

class ProductImporter extends MainProductImporter implements ShopImporterInterface, ImporterInterface
{
    /** @var ClientAdapter */
    protected $client;

    /** @var int */
    protected $languageOuterId = 0;

    public function import()
    {
        $this->initCollections();
        $this->client->init();
        $this->loadLanguageId();
        $this->collectProductItems();
        $this->collectProducts();
        $this->createImportLog();
    }

    protected function loadLanguageId()
    {
        $sql = "
        SELECT
            l.language_id
        FROM
            setting as s
            LEFT JOIN `language` as l
                ON s.value = l.code
        WHERE
            s.key = 'config_admin_language'
        LIMIT 0,1
        ";
        $data = $this->client->getRequest($sql);
        if (isset($data['language_id'])) {
            $this->languageOuterId = $data['language_id'];
        }
    }

    protected function collectProductItems()
    {
        if ($this->hasInProgressItemRequests()) {
            return;
        }
        $this->setCollectionLogIndex(1);
        $this->setItemLogIndex(1);
        $list = $this->getProducts();
        $this->addItemsToProcessCollection($list);
        $this->saveItemsToProcess();
        $this->setCollectionLogFinish();
        $this->entityManager->flush();
    }

    protected function collectProducts()
    {
        if ($this->itemProcessCollection->count()) {
            $items = $this->itemProcessCollection->toArray();
            $counterToFlush = 0;
            foreach ($items as $key => $item) {
                $index = $item->getItemIndex();
                $value = $item->getItemValue();
                if ($this->isInLimits()) {
                    $this->setItemLogIndex($index);
                    $data = $this->getProductData($value);
                    $this->setProcessed($item, $key);
                    if ($this->isAllowed($data)) {
                        $this->setProduct(
                            array(
                                'sku' => $data['sku'],
                                'name' => $data['name'],
                                'picture' => $data['image'],
                                'url' => $data['url'],
                                'manufacturer' => $data['manufacturer'],
                                'category' => $data['category'],
                                'availableDate' => $data['date_available'],
                                'outerId' => $data['product_id'],
                                'productCreateDate' => $data['date_added'],
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
        if ($this->timeOut == 0) {
            $this->setItemLogFinish();
        }
        $this->entityManager->flush();
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getProducts()
    {
        $sql = "
        SELECT
            p.product_id
        FROM
            product as p
        WHERE
            (
                p.date_added > '0000-00-00 00:00:00'
                OR p.date_modified > '0000-00-00 00:00:00'
            )
            AND p.status = 1
            AND p.date_available <= NOW()
        ORDER BY p.product_id ASC
        ";
        $list = $this->client->getCollectionRequest($sql);
        return $list;
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    protected function getProductData($id)
    {
        $sql = "
                    SELECT
                        p.product_id,
                        p.sku,
                        p.date_available,
                        p.image,
                        p.status,
                        pd.name,
                        m.name as manufacturer,
                        p.date_added,
                        (
                            SELECT keyword
                            FROM url_alias
                            WHERE query = 'product_id=" . $id . "' LIMIT 0,1
                        ) AS url,
                        (
                            SELECT cd.name
                            FROM product_to_category as ptc
                            LEFT JOIN category as c
                                ON ptc.category_id = c.category_id
                            LEFT JOIN category_description as cd
                                ON ptc.category_id = cd.category_id
                            WHERE
                                ptc.product_id = " . $id . "
                                AND cd.language_id = " . $this->languageOuterId. "
                                AND c.status = 1
                            ORDER BY c.sort_order ASC, c.category_id DESC
                            LIMIT 0,1
                        ) AS category
                    FROM
                        product as p
                        LEFT JOIN product_description as pd
                            ON p.product_id = pd.product_id
                        LEFT JOIN manufacturer as m
                            ON p.manufacturer_id = m.manufacturer_id
                    WHERE
                        p.product_id = " . $id . "
                        AND pd.language_id = " . $this->languageOuterId. "
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
            $item = $this->setImportItemProcess($index + 1, $value['product_id']);
            $this->itemProcessCollection->add($item);
        }
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isAllowed($data)
    {
        if (
            !isset($data['product_id'])
            || !isset($data['sku'])
        ) {
            return false;
        }
        return true;
    }
}