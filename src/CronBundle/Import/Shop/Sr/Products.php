<?php

namespace CronBundle\Import\Shop\Sr;

use CronBundle\Import\Shop\Products as ShopProducts;
use CronBundle\Import\Shop\Sr\ClientAdapter;
use Symfony\Component\Validator\Constraints\DateTime;
use AppBundle\Entity\ImportLog;
use CronBundle\Service\Benchmark;

class Products extends ShopProducts
{
    /** @var ClientAdapter */
    protected $client;

    /** @var int */
    protected $sleep = 0;

    /** @var int */
    protected $languageInnerId = 1;

    /** @var string */
    protected $languageApiId = '';

    /** @var Benchmark */
    protected $benchmark;

    /**
     * @param $service
     */
    public function setBenchmark($service)
    {
        $this->benchmark = $service;
    }

    public function import()
    {
        $this->client->init();

        $this->collectProductItems();
        $this->collectProducts();
        $this->entityManager->flush();
        $this->createImportLog();
    }

    protected function collectProductItems()
    {
        if ($this->hasInProgressItemRequests()) {
            $this->collectLastItems();
            return;
        }

        $page = 0;
        if ($this->hasInProgressCollectionRequests()) {
            $page = $this->getNextCollectionIndexFromLog();
        }
        $limit = 200;
        $do = 1;
        while ($do == 1) {
            $this->setCollectionLogIndex($page);
            $this->actualTime = round(microtime(true) - $this->startTime);
            if ($this->actualTime < $this->timeLimit) {
                $list = $this->client->getCollectionRequest('products?page=' . $page . '&limit=' . $limit);
                if (!isset($list['items'])) {
                    $do = 0;
                } else {
                    $this->items = array_merge($this->items, $list['items']);
                    $page++;
                    sleep($this->sleep);
                }
            } else {
                $this->timeOut = 1;
                $do = 0;
            }
            //$do = 0;
        }
        $this->saveItemsToProcess();
        // Időtúllépés nélkül végig futott => nincs több lekérendő collection
        // Befejezés dátumát beállítjuk az import_collection_logban
        if ($this->timeOut == 0) {
            $this->setCollectionLogFinish();
        }
    }

    protected function collectProducts()
    {
        if ($this->items) {
            $this->collectLanguageId();
            foreach ($this->items as $index => $item) {
                $this->setItemLogIndex($index);
                $this->actualTime = round(microtime(true) - $this->startTime);
                if ($this->actualTime < $this->timeLimit) {
                    $data = $this->client->getRequest($item['href']);
                    $this->processedItems[] = $index;
                    if (!isset($data['id'])) {
                        continue;
                    }
                    $name = '';
                    $url = '';
                    $picture = '';
                    if (isset($data['mainPicture'])) {
                        $picture = $data['mainPicture'];
                    }
                    $descriptionList = $this->client->getCollectionRequest(
                        'productDescriptions?page=0&limit=1&productId='
                        . $data['id'] . '&languageId=' . $this->languageApiId
                    );
                    if (isset($descriptionList['items'][0]['href'])) {
                        $descriptionData = $this->client->getRequest($descriptionList['items'][0]['href']);
                        if (isset($descriptionData['name'])) {
                            $name = $descriptionData['name'];
                        }
                    }
                    $urlList = $this->client->getCollectionRequest(
                        'urlAliases?page=0&limit=1&productId='
                        . $data['id']
                    );
                    if (isset($urlList['items'][0]['href'])) {
                        $urlData = $this->client->getRequest($urlList['items'][0]['href']);
                        if (isset($urlData['urlAlias'])) {
                            $url = $urlData['urlAlias'];
                        }
                    }
                    if ($this->isAllowed($data)) {
                        $dataToSave = array(
                            'sku' => $data['sku'],
                            'name' => $name,
                            'picture' => $picture,
                            'url' => $url,
                            'availableDate' => $data['availableDate'],
                            'outerId' => $data['id'],
                        );
                        $this->setProduct($dataToSave);
                    }
                    sleep($this->sleep);
                } else {
                    $this->timeOut = 1;
                    break;
                }
            }
            if ($this->timeOut == 0) {
                // Időtúllépés nélkül végig futott => nincs több lekérendő item
                // Ürítjük az import_item_process táblát
                $this->emptyProcessedItems();
                // Befejezés dátumát beállítjuk az import_item_logban
                $this->setItemLogFinish();
            }
        }
    }

    protected function createImportLog()
    {
        $runtime = microtime(true) - $this->startTime;
        $processed = count($this->processedItems);
        $unprocessed = count($this->items) - $processed;
        $log = new ImportLog();
        $log->setImportName('products');
        $log->setRunTime($runtime);
        $log->setProcessed($processed);
        $log->setUnprocessed($unprocessed);
        $this->entityManager->persist($log);
        $this->entityManager->flush();
        $this->benchmark->runtime = $runtime;
        $this->benchmark->lastIndex = $this->getLastItemIndexFromLog();
        $this->benchmark->processItemCount = $processed;
    }

    protected function collectLanguageId()
    {
        $list = $this->client->getCollectionRequest('languages?page=0&limit=200&innerId=' . $this->languageInnerId);
        $languageHref = $list['items'][0]['href'];

        $data = $this->client->getRequest($languageHref);
        $this->languageApiId = $data['id'];
    }

    protected function isAllowed($data)
    {
        if ($data['status'] != 1) {
            return false;
        }
        /*
        if ($data['availableDate']) {
            $now = new \DateTime();
            $available = new \DateTime($data['availableDate']);
            if ($now->getTimestamp() < $available->getTimestamp()) {
                return false;
            }
        }
        */
        return true;
    }
}