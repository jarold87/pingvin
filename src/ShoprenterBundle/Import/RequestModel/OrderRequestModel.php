<?php

namespace ShoprenterBundle\Import\RequestModel;

use CronBundle\Import\RequestModel;


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
     * @param string $key
     * @return string
     * @throws \Exception
     */
    public function getItemRequest($key = '')
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
                        o.order_id = " . $key . "
                    LIMIT 0,1
        ";
        $this->item = $sql;
        return parent::getItemRequest($key);
    }
}