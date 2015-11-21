<?php

namespace CronBundle\Import;


interface ClientAdapterInterface
{
    public function init();

    public function getCollectionRequest($request);

    public function getRequest($request);
}