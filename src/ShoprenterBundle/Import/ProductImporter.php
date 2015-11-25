<?php

namespace ShoprenterBundle\Import;

use AppBundle\Entity\ImportItemProcess;
use CronBundle\Import\ProductImporter as MainProductImporter;
use CronBundle\Import\ImporterInterface;

class ProductImporter extends MainProductImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'product_id';

    /** @var ClientAdapter */
    protected $client;

    /** @var int */
    protected $languageOuterId = 0;

    public function import()
    {
        $this->initConverter();
        $this->initCollections();
        $this->client->init();
        $this->loadLanguageId();
        $this->collectProductItems();
        $this->collectProducts();
        $this->saveImportLog();
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
        $this->saveCollectionItems();
    }

    protected function collectProducts()
    {
        if (!$this->isInLimits()) {
            $this->timeOut = 1;
            return;
        }
        $this->loadItemsToProcessCollection();
        if (!$this->itemProcessCollection->count()) {
            return;
        }
        $this->loadExistEntityCollection();
        $items = $this->itemProcessCollection->toArray();
        $this->collectByItems($items);
        if ($this->isFinishedImport()) {
            $this->setItemLogFinish();
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
            $responseData = $this->getItemData($item, $key);
            $this->setProcessed($item, $key);
            $this->responseDataConverter->setResponseData($responseData);
            $data = $this->responseDataConverter->getConvertedData();
            if (!$this->isAllowed($data)) {
                continue;
            }
            $this->setProduct($data);
            $this->manageFlush();
        }
    }

    /**
     * @param ImportItemProcess $item
     * @return mixed
     */
    protected function getItemData(ImportItemProcess $item)
    {
        $index = $item->getItemIndex();
        $value = $item->getItemValue();
        $this->setItemLogIndex($index);
        return $this->getProductData($value);
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
}