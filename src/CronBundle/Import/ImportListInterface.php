<?php

namespace CronBundle\Import;

interface ImportListInterface
{
    public function getNumberOfImports();

    public function setImportIndex($index);

    public function getImport();

    public function getImportSourceType();
}