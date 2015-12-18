<?php

namespace CronBundle\Import;

abstract class ImportList
{
    /**
     * @var array
     *
     * [1] -> Importer neve: Meg kell egyeznie az importer fájl nevének elejével.
     * [2] -> Import forrása (shop / GA)
     * [3] -> Importer bundle
     * pl.: 1 => array('Product', 'shop', 'ShoprenterBundle')
     */
    protected $imports = array();

    /** @var int */
    protected $importIndex = 0;

    /** @var string */
    protected $importerClassNameSpace;

    /** @var string */
    protected $importerComponentFactoryNameSpace;

    /**
     * @param $string
     */
    public function setImporterClassNameSpace($string)
    {
        $this->importerClassNameSpace = $string;
    }

    /**
     * @param $string
     */
    public function setImporterComponentFactoryNameSpace($string)
    {
        $this->importerComponentFactoryNameSpace = $string;
    }

    /**
     * @param $index
     */
    public function setImportIndex($index)
    {
        $this->importIndex = $index;
    }

    /**
     * @return int
     */
    public function getNumberOfImports()
    {
        return count($this->imports);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getImport()
    {
        if (!isset($this->imports[$this->importIndex])) {
            throw new \Exception('Missing import!');
        }
        $importName = $this->imports[$this->importIndex][0];
        $bundleName = $this->imports[$this->importIndex][1];
        $importerClassName = $bundleName . '\\' . $this->importerClassNameSpace . '\\' . $importName . 'Importer';
        $importerComponentFactoryClassName = $bundleName . '\\' . $this->importerComponentFactoryNameSpace . '\\' . $importName . 'ComponentFactory';
        try {
            $importer = new $importerClassName();
            $factory = new $importerComponentFactoryClassName();
            $importer->setComponentFactory($factory);
            $importer->setImportName($this->imports[$this->importIndex][0]);
            return $importer;
        } catch (\Exception $e) {
            throw new \Exception('UnSuccess importer create!' . $e->getMessage());
        }
    }
}