<?php

namespace CronBundle\Import\Shop;


interface ClientAdapterInterface
{
    public function init();

    public function getCollectionRequest($request);

    public function getRequest($request);
}