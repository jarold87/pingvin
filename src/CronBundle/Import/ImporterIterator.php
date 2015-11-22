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
     * @throws Exception
     */
    public function setActualImportIndex($index)
    {
        $index = intval($index);
        if (!$index) {
            throw new Exception("Not a valid import index!");
        }
        $this->actualIndex = $index;
    }

    /**
     * @return int
     */
    public function getActualImportIndex()
    {
        $nextIndex = $this->actualIndex;
        return $nextIndex--;
    }

    /**
     * @return null|ProductImporter|\ShoprenterBundle\Import\UrlImporter|void
     */
    public function getNextImport()
    {
        if (!$this->hasNextImport()) {
            return null;
        }
        $index = $this->importList->getImport($this->actualIndex);
        $this->actualIndex++;
        return $index;
    }

    /**
     * @return bool
     */
    public function hasNextImport()
    {
        $nextIndex = $this->actualIndex;
        if ($nextIndex > $this->importList->getNumberOfImports()) {
            return false;
        }
        return true;
    }
}