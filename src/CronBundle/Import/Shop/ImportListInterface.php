<?php

namespace CronBundle\Import\Shop;

interface ImportListInterface
{
    public function getNumberOfImports();

    public function getImport($index);
}