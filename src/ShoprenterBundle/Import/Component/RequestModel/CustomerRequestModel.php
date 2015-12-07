<?php

namespace ShoprenterBundle\Import\Component\RequestModel;

use CronBundle\Import\Component\RequestModel;


class CustomerRequestModel extends RequestModel
{
    /**
     * @return string
     * @throws \Exception
     */
    public function getCollectionRequest()
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
                        c.customer_id = " . $key . "
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
                        c.customer_id IN (" . join(', ', $keys) . ")
                    ORDER BY c.customer_id ASC
        ";
        $this->itemPackage = $sql;
        return parent::getItemPackageRequest($keys);
    }
}