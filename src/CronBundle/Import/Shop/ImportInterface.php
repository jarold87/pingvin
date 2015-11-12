<?php

namespace CronBundle\Import\Shop;


interface ImportInterface
{
    public function setEntityManager($entityManager);

    public function setClient($client);

    public function setStartTime($startTime);

    public function setActualTime($actualTime);

    public function setTimeLimit($timeLimit);

    public function import();

    public function isFinishedImport();
}