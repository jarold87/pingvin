<?php

namespace CronBundle\Import;

use ShoprenterBundle\Import\ProductImporter;
use ShoprenterBundle\Import\ImportList;

class ImporterIterator
{
    protected $importList;

    /** @var int */
    protected $actualIndex = 1;

    public function __construct(ImportList $importList)
    {
        $this->importList = $importList;
    }

    /**
     * @param $index
     * @throws \Exception
     */
    public function setActualImportIndex($index)
    {
        $index = intval($index);
        if (!$index) {
            throw new \Exception("Not a valid import index!");
        }
        $this->actualIndex = $index;
    }

    public function setNextImportIndex()
    {
        $this->actualIndex++;
    }

    /**
     * @return int
     */
    public function getActualImportIndex()
    {
        return $this->actualIndex;
    }

    /**
     * @return null|\ShoprenterBundle\Import\CustomerImporter|\ShoprenterBundle\Import\OrderImporter|\ShoprenterBundle\Import\OrderProductImporter|ProductImporter|void
     */
    public function getActualImport()
    {
        if (!$this->hasImport()) {
            return null;
        }
        $this->importList->setImportIndex($this->actualIndex);
        return $this->importList->getImport();
    }

    /**
     * @return mixed|null
     */
    public function getActualImportSourceType()
    {
        if (!$this->hasImport()) {
            return null;
        }
        $this->importList->setImportIndex($this->actualIndex);
        return $this->importList->getImportSourceType();
    }

    /**
     * @return bool
     */
    public function hasImport()
    {
        if ($this->actualIndex > $this->importList->getNumberOfImports()) {
            return false;
        }
        return true;
    }
}