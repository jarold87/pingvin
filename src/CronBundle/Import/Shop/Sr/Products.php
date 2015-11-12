<?php

namespace CronBundle\Import\Shop\Sr;

use CronBundle\Import\Shop\Products as ShopProducts;
use CronBundle\Import\Shop\Sr\ClientAdapter;
use Symfony\Component\Validator\Constraints\DateTime;

class Products extends ShopProducts
{
    /** @var ClientAdapter */
    protected $client;

    /** @var int */
    protected $sleep = 1;

    public function import()
    {
        $this->client->init();
        $this->collectProductItems();
        $this->collectProducts();
        $this->saveProducts();
        echo '<hr>Items: ';
        var_dump('<pre>', $this->items);
        echo '<hr>Processed items: ';
        var_dump('<pre>', $this->processedItems);
        echo '<hr>Termékek: ';
        var_dump('<pre>', $this->products);
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
        $limit = 1;
        $do = 1;
        while ($do == 1) {
            $this->actualTime = round(microtime(true) - $this->startTime);
            if ($this->actualTime < $this->timeLimit) {
                $response = $this->client->getCollectionRequest('products?page=' . $page . '&limit=' . $limit);
                $list = $response->getParsedResponseBody();
                if (!isset($list['items'])) {
                    $do = 0;
                } else {
                    $this->items = array_merge($this->items, $list['items']);
                    $page++;
                    sleep($this->sleep);
                }
            } else {
                $this->timeOut = 1;
                // Az utolsó index-et (page) rögzítjük az import_collection_logban
                $do = 0;
            }
            // Időtúllépés nélkül végig futott => nincs több lekérendő collection
            // Befejezés dátumát beállítjuk az import_collection_logban
            // Az utolsó index-et rögzítjük az import_collection_logban
        }
        $this->saveItemsToProcess();
    }

    protected function collectProducts()
    {
        if ($this->items) {
            foreach ($this->items as $index => $item) {
                $this->actualTime = round(microtime(true) - $this->startTime);
                if ($this->actualTime < $this->timeLimit) {
                    $response = $this->client->getRequest($item['href']);
                    $data = $response->getParsedResponseBody();
                    if ($this->isAllowed($data)) {
                        // Termék leírást külön le kell kérdezni
                        $this->products[] = array(
                            'itemIndex' => $index,
                            'href' => $data['href'],
                            'sku' => $data['sku'],
                            'mainPicture' => $data['mainPicture'],
                            'availableDate' => $data['availableDate'],
                            'productDescriptions' => $data['productDescriptions'],
                            'status' => $data['status'],
                        );
                    }
                    $this->processedItems[] = $index;
                    sleep($this->sleep);
                } else {
                    $this->timeOut = 1;
                    // Az utolsó index-et rögzítjük az import_item_logban
                    break;
                }
                // Időtúllépés nélkül végig futott => nincs több lekérendő item
                // Ürítjük az import_item_process táblát
                $this->emptyProcessedItems();
                // Befejezés dátumát beállítjuk az import_item_logban
                // Az utolsó index-et rögzítjük az import_item_logban
            }
        }
    }

    protected function saveProducts()
    {
        // Adatbázisba elmentjük a termékeket
    }

    protected function isAllowed($data)
    {
        if ($data['status'] != 1) {
            return false;
        }
        if ($data['availableDate']) {
            $now = new \DateTime();
            $available = new \DateTime($data['availableDate']);
            if ($now->getTimestamp() < $available->getTimestamp()) {
                return false;
            }
        }
        return true;
    }
}