<?php

namespace ShoprenterBundle\Import\Component\RequestModel;

use CronBundle\Import\Component\RequestModel;


class OrderProductRequestModel extends RequestModel
{
    /**
     * @return string
     * @throws \Exception
     */
    public function getCollectionRequest()
    {
        $sql = "
        SELECT
            op.order_product_id
        FROM
            order_product as op
            LEFT JOIN `order` as o
                ON op.order_id = o.order_id
        WHERE
            (
                o.date_added > '0000-00-00 00:00:00'
                OR o.date_modified > '0000-00-00 00:00:00'
            )
            AND o.order_status_id != 0
        ORDER BY o.order_id ASC
        ";
        $this->collection = $sql;
        return parent::getCollectionRequest();
    }

    /**
     * @param string $key
     * @return string
     * @throws \Exception
     */
    public function getItemRequest($key = '')
    {
        $sql = "
                    SELECT
                        op.order_product_id,
                        op.order_id,
                        op.product_id,
                        op.quantity,
                        op.total,
                        op.date_added
                    FROM
                        order_product as op
                    WHERE
                        op.order_product_id = " . $key . "
                    LIMIT 0,1
        ";
        $this->item = $sql;
        return parent::getItemRequest($key);
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
                        op.order_product_id,
                        op.order_id,
                        op.product_id,
                        op.quantity,
                        op.total,
                        op.date_added
                    FROM
                        order_product as op
                    WHERE
                        op.order_product_id IN (" . join(', ', $keys) . ")
                    ORDER BY op.order_product_id ASC
        ";
        $this->itemPackage = $sql;
        return parent::getItemPackageRequest($keys);
    }
}