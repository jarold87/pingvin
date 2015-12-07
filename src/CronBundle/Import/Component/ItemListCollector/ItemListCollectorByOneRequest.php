<?php

namespace CronBundle\Import\Component\ItemListCollector;

class ItemListCollectorByOneRequest extends ItemListCollector
{
    public function init()
    {
        parent::init();
    }

    public function collect()
    {
        if ($this->isInProgressItemProcess()) {
            return;
        }
        $this->setCollectionLogIndex(1);
        $request = $this->requestModel->getCollectionRequest();
        $list = $this->client->getCollectionRequest($request);
        if ($this->client->getError()) {
            $this->addError($this->client->getError());
            return;
        }
        $this->addToProcessCollection($list);
        $this->saveItemsToProcess();
        $this->setItemLogIndex(1);
        $this->setCollectionLogFinish();
        parent::collect();
    }
}