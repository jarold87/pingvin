<?php

namespace CronBundle\Import\ItemCollector;


class SinglyItemCollector
{

    /**
     * @param $items
     */
    protected function collectItemsBySinglyRequests($items)
    {
        foreach ($items as $key => $item) {
            if (!$this->isInLimits()) {
                $this->timeOut = 1;
                break;
            }
            $responseData = $this->getItemData($item);
            if ($this->client->getError()) {
                $this->addError($this->client->getError());
                return;
            }
            if (!$responseData) {
                $this->importLog->addEmptyResponse($this->importName, $item->getItemValue());
                $this->setProcessed($item, $key);
                continue;
            }
            $this->responseDataConverter->setOuterId($responseData[$this->outerIdKey]);
            $this->responseDataConverter->setResponseData($responseData);
            $data = $this->responseDataConverter->getConvertedData();
            if (!$this->isAllowed($data)) {
                $this->importLog->addNotAllowed($this->importName, $item->getItemValue());
                continue;
            }
            $this->setEntity($data);
            $this->setProcessed($item, $key);
            $this->manageFlush();
        }
    }

    /**
     * @param ImportItemProcess $item
     * @return mixed
     */
    protected function getItemData(ImportItemProcess $item)
    {
        $key = $item->getItemValue();
        $request = $this->requestModel->getItemRequest($key);
        return $this->client->getRequest($request);
    }
}