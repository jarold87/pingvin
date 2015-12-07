<?php

namespace CronBundle\Import;

interface ImportListInterface
{
    public function setImporterClassNameSpace($string);

    public function setImporterComponentFactoryNameSpace($string);

    public function setImportIndex($index);

    public function getNumberOfImports();

    public function getImport();
}