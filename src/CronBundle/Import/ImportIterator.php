<?php

namespace CronBundle\Import;

use CronBundle\Import\Shop\Sr\Products;
use CronBundle\Import\Shop\Sr\ImportList;

class ImportIterator
{
    protected $importList;

    /** @var int */
    protected $actualIndex = 0;

    public function __construct(ImportList $importList)
    {
        $this->importList = $importList;
    }

    /**
     * @param $index
     */
    public function setActualImportIndex($index)
    {
        $this->actualIndex = $index;
    }

    /**
     * @return int
     */
    public function getActualImportIndex()
    {
        return $this->actualIndex;
    }

    /**
     * @return Products|Shop\Sr\Urls|null|void
     */
    public function getNextImport()
    {
        if (!$this->hasNextImport()) {
            return null;
        }
        $this->actualIndex++;
        return $this->importList->getImport($this->actualIndex);
    }

    /**
     * @return bool
     */
    public function hasNextImport()
    {
        $nextIndex = $this->actualIndex + 1;
        if ($nextIndex > $this->importList->getNumberOfImports()) {
            return false;
        }
        return true;
    }
}