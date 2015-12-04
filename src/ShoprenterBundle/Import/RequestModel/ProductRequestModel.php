<?php

namespace ShoprenterBundle\Import\RequestModel;

use CronBundle\Import\RequestModel;


class ProductRequestModel extends RequestModel
{
    /** @var int */
    protected $languageOuterId = 0;

    /**
     * @param $id
     */
    public function setLanguageOuterId($id)
    {
        $this->languageOuterId = $id;
    }

    /**
     * @return string
     */
    public function getLanguageRequest()
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
        $this->language = $sql;
        return parent::getLanguageRequest();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCollectionRequest()
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
            AND p.date_available <= NOW()
        ORDER BY p.product_id ASC
        ";
        $this->collection = $sql;
        return parent::getCollectionRequest();
    }

    /**
     * @param array $keys
     * @return string
     * @throws \Exception
     */
    public function getItemPackageRequest(array $keys)
    {
        $sql = "
                     SELECT
                        p.product_id,
                        p.sku,
                        p.image,
                        p.status,
                        pd.name,
                        m.name as manufacturer,
                        p.date_added,
                        p.date_available,
                        IF (LENGTH(pd.short_description) > 0 OR LENGTH(pd.description) > 0,1,0) as is_description,
                        (
                            SELECT keyword
                            FROM url_alias
                            WHERE query = CONCAT('product_id=', p.product_id) LIMIT 0,1
                        ) AS url,
                        (
                            SELECT c.category_id
                            FROM product_to_category as ptc
                            LEFT JOIN category as c
                                ON ptc.category_id = c.category_id
                            WHERE
                                ptc.product_id = p.product_id
                                AND c.status = 1
                            ORDER BY c.sort_order ASC, c.category_id DESC
                            LIMIT 0,1
                        ) AS category_id,
                        (
                            SELECT cd.name
                            FROM product_to_category as ptc
                            LEFT JOIN category as c
                                ON ptc.category_id = c.category_id
                            LEFT JOIN category_description as cd
                                ON ptc.category_id = cd.category_id
                            WHERE
                                ptc.product_id = p.product_id
                                AND cd.language_id = " . $this->languageOuterId. "
                                AND c.status = 1
                            ORDER BY c.sort_order ASC, c.category_id DESC
                            LIMIT 0,1
                        ) AS category
                    FROM
                        product as p
                        LEFT JOIN product_description as pd
                            ON p.product_id = pd.product_id AND pd.language_id = " . $this->languageOuterId. "
                        LEFT JOIN manufacturer as m
                            ON p.manufacturer_id = m.manufacturer_id
                    WHERE
                        p.product_id IN (" . join(', ', $keys) . ")
                    ORDER BY p.product_id ASC
                    ";
        $this->itemPackage = $sql;
        return parent::getItemPackageRequest($keys);
    }
}