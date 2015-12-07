<?php

namespace CronBundle\Import\Component\ItemCollector;

class ItemCollectorByOnePerRequest extends ItemCollector
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
        $this->collectItemsByOnePerRequest($items);
        parent::collect();
    }

    public function collectDeadItem()
    {
        parent::collectDeadItem();
    }

    /**
     * @param $items
     */
    protected function collectItemsByOnePerRequest($items)
    {
        foreach ($items as $key => $item) {
            if (!$this->isInLimits()) {
                break;
            }
            $request = $this->requestModel->getItemRequest($this->getImportProcessValue($item));
            $responseData = $this->client->getRequest($request);
            if ($this->client->getError()) {
                $this->addError($this->client->getError());
                return;
            }
            if (!$responseData) {
                $this->importLog->addEmptyResponse($this->importName, $this->getImportProcessValue($item));
                $this->setProcessed($item, $key);
                continue;
            }
            $this->responseDataConverter->setOuterId($responseData[$this->outerIdKey]);
            $this->responseDataConverter->setResponseData($responseData);
            $data = $this->responseDataConverter->getConvertedData();
            if (!$this->isAllowed($data)) {
                $this->importLog->addNotAllowed($this->importName, $this->getImportProcessValue($item));
                continue;
            }
            $this->setEntity($data);
            $this->setProcessed($item, $key);
            $this->manageFlush();
        }
    }
}