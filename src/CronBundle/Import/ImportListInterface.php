<?php

namespace CronBundle\Import;

interface ImportListInterface
{
    public function getNumberOfImports();

    public function getImport($index);
}