<?php

namespace ShoprenterBundle\Import\Component\RequestModel;

use CronBundle\Import\Component\RequestModel;


class OrderRequestModel extends RequestModel
{
    /**
     * @return string
     * @throws \Exception
     */
    public function getCollectionRequest()
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
                        o.order_id,
                        o.customer_id,
                        o.shipping_method,
                        o.payment_method,
                        o.currency,
                        o.date_added
                    FROM
                        `order` as o
                    WHERE
                        o.order_id IN (" . join(', ', $keys) . ")
                    ORDER BY o.order_id ASC
        ";
        $this->itemPackage = $sql;
        return parent::getItemPackageRequest($keys);
    }
}