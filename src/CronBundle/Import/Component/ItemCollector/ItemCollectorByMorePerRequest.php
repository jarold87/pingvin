<?php

namespace CronBundle\Import\Component\ItemCollector;

class ItemCollectorByMorePerRequest extends ItemCollector
{
    public function init()
    {
        $this->initExistEntityCollection();
        $this->initItemProcessCollection();
        parent::init();
    }

    public function collect()
    {
        if (!$this->itemProcessCollection->count()) {
            return;
        }
        $items = $this->itemProcessCollection->toArray();
        $this->collectItemsByMorePerRequest($items);
        parent::collect();
    }

    public function collectDeadItem()
    {
        parent::collectDeadItem();
    }

    /**
     * @param $items
     */
    protected function collectItemsByMorePerRequest($items)
    {
        $package = array();
        foreach ($items as $index => $values) {
            if (!$this->isInLimits()) {
                break;
            }
            $package[$index] = $values;
            if (count($package) < $this->flushItemPackageNumber && count($package) < count($items)) {
                continue;
            }
            $this->processItemPackage($package);
            $package = array();
        }
    }

    protected function processItemPackage(array $package)
    {
        $itemValues = array();
        $itemProcessStatus = array();
        foreach ($package as $item) {
            $itemValues[] = $this->getImportProcessValue($item);
            $itemProcessStatus[$this->getImportProcessValue($item)] = 0;
        }
        $request = $this->requestModel->getItemPackageRequest($itemValues);
        $list = $this->client->getPackageRequest($request);
        if ($this->client->getError()) {
            $this->addError($this->client->getError());
            return;
        }
        foreach ($list as $responseData) {
            $this->responseDataConverter->setOuterId($responseData[$this->outerIdKey]);
            $this->responseDataConverter->setResponseData($responseData);
            $data = $this->responseDataConverter->getConvertedData();
            if (isset($itemProcessStatus[$data['outerId']])) {
                $itemProcessStatus[$data['outerId']] = 1;
            }
            if (!$this->isAllowed($data)) {
                $this->importLog->addNotAllowed($this->importName, $data['outerId']);
                continue;
            }
            $this->setEntity($data);
        }
        foreach ($package as $key => $item) {
            $this->setProcessed($item, $key);
            if (!$itemProcessStatus[$this->getImportProcessValue($item)]) {
                $this->importLog->addEmptyResponse($this->importName, $this->getImportProcessValue($item));
            }
        }
        $this->manageFlush();
    }
}