<?php

namespace CronBundle\Import\Component;


abstract class RequestModel
{
    /** @var string */
    protected $language = '';

    /** @var string */
    protected $collection = '';

    /** @var string */
    protected $item = '';

    /** @var string */
    protected $itemPackage = '';

    public function getLanguageRequest()
    {
        if (!$this->language) {
            throw new \Exception("Not a valid request!");
        }
        return $this->language;
    }

    public function getCollectionRequest()
    {
        if (!$this->collection) {
            throw new \Exception("Not a valid request!");
        }
        return $this->collection;
    }

    public function getItemRequest($key)
    {
        if (!$key) {
            throw new \Exception("Not a valid key!");
        }
        if (!$this->item) {
            throw new \Exception("Not a valid request!");
        }
        return $this->item;
    }

    public function getItemPackageRequest(array $keys)
    {
        if (!$keys) {
            throw new \Exception("Not valid keys!");
        }
        if (!$this->itemPackage) {
            throw new \Exception("Not a valid request!");
        }
        return $this->itemPackage;
    }
}