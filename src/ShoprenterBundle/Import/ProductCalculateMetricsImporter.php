<?php
namespace ShoprenterBundle\Import;

use CronBundle\Import\Importer;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductStatistics;
use AppBundle\Entity\OrderProduct;

class ProductCalculateMetricsImporter extends Importer
{
    /** @var int */
    protected $itemProcessLimit = 10;

    /** @var ArrayCollection */
    protected $productEntityCollection;

    /** @var array */
    protected $AllItemProcessCount = array();

    /** @var array */
    protected $collectData = array();

    public function init()
    {
        $this->initProductEntityCollection();
    }

    public function import()
    {
        $this->collectItems();

        if ($this->getError()) {
            $this->saveImportLog();
            return;
        }

        $this->collectItemData();

        if ($this->getError()) {
            $this->saveImportLog();
            return;
        }

        if ($this->isFinishedImport()) {
            $this->itemCollector->setItemLogFinish();
        }
        $this->saveImportLog();
    }

    protected function collectItems()
    {
        // TODO Alapból collectionLog

        $products = $this->productEntityCollection->toArray();
        foreach ($products as $product) {

            // TODO Time limit nézése

            $productId = $this->getProductId($product);
            $statisticsCollection = $this->getProductStatisticsCollection($product);
            $statisticsArray = $statisticsCollection->toArray();
            if ($statisticsArray) {
                foreach ($statisticsArray as $statistics) {
                    $timeKey = $this->getTimeKey($statistics);
                    $views = $this->getViews($statistics);
                    $uniqueViews = $this->getUniqueViews($statistics);
                    $orders = $this->getOrderCount($product, $timeKey);
                    $uniqueOrders = $this->getUniqueOrderCount($product, $timeKey);
                    if ($uniqueOrders > $uniqueViews) {
                        $uniqueViews = $uniqueOrders;
                    }
                    if ($orders > $views) {
                        $views = $orders;
                    }
                    $conversion = $this->getConversion($uniqueOrders, $uniqueViews);
                    $statisticsArray = array(
                        //'object' => '',
                        'object' => $statistics,
                        'views' => $views,
                        'uniqueViews' => $uniqueViews,
                        'orders' => $orders,
                        'uniqueOrders' => $uniqueOrders,
                        'conversion' => $conversion,
                    );
                    $this->collectData[$productId][$timeKey] = $statisticsArray;
                }
            }

            // TODO itemLog
        }
        //var_dump('<pre>', $this->collectData); exit;
        if ($this->collectData) {
            $productIds = array_keys($this->collectData);
            foreach ($productIds as $productId) {
                foreach ($this->collectData[$productId] as $timeKey => $values) {
                    $object = $values['object'];
                    $this->setProductStatisticsObject($object, $values);
                }
            }
        }
        $this->entityManager->flush();
    }

    protected function setProductStatisticsObject(ProductStatistics $object, $values)
    {
        $object->setViews($values['views']);
        $object->setUniqueViews($values['uniqueViews']);
        $object->setOrders($values['orders']);
        $object->setUniqueOrders($values['uniqueOrders']);
        $object->setConversion($values['conversion']);
        $this->entityManager->persist($object);
    }

    protected function getProductStatisticsCollection(Product $product)
    {
        return $product->getProductStatistics();
    }

    protected function getProductId(Product $product)
    {
        return $product->getProductId();
    }

    protected function getTimeKey(ProductStatistics $productStatistics)
    {
        return $productStatistics->getTimeKey();
    }

    protected function getViews(ProductStatistics $productStatistics)
    {
        return $productStatistics->getViews();
    }

    protected function getUniqueViews(ProductStatistics $productStatistics)
    {
        return $productStatistics->getUniqueViews();
    }

    /**
     * @param Product $product
     * @param $timeKey
     * @return int
     */
    protected function getOrderCount(Product $product, $timeKey)
    {
        $productOrders = $product->getProductOrders();
        if (!$productOrders->count()) {
            return 0;
        }
        $startDate = $this->getStartDateByTimeKey($timeKey);
        $finishDate = $this->getFinishDateByTimeKey($timeKey);
        $counter = 0;
        foreach ($productOrders->toArray() as $orderProduct)
        {
            if (!$startDate || !$finishDate) {
                $counter += $this->getQuantity($orderProduct);
                continue;
            }
            $orderDate = $this->getOrderProductOrderDate($orderProduct);
            if ($orderDate >= $startDate && $orderDate <= $finishDate) {
                $counter += $this->getQuantity($orderProduct);
            }
        }
        return $counter;
    }

    /**
     * @param Product $product
     * @param $timeKey
     * @return int
     */
    protected function getUniqueOrderCount(Product $product, $timeKey)
    {
        $productOrders = $product->getProductOrders();
        if (!$productOrders->count()) {
            return 0;
        }
        $startDate = $this->getStartDateByTimeKey($timeKey);
        $finishDate = $this->getFinishDateByTimeKey($timeKey);
        if (!$startDate || !$finishDate) {
            return $productOrders->count();
        }
        $counter = 0;
        foreach ($productOrders->toArray() as $orderProduct)
        {
            $orderDate = $this->getOrderProductOrderDate($orderProduct);
            if ($orderDate >= $startDate && $orderDate <= $finishDate) {
                $counter++;
            }
        }
        return $counter;
    }

    protected function getConversion($orderCount, $views)
    {
        if ($orderCount) {
            return round($orderCount / $views * 100, 2);
        }
        return 0;
    }

    protected function getOrderProductOrderDate(OrderProduct $orderProduct)
    {
        return $orderProduct->getOrderDate();
    }

    protected function getStartDateByTimeKey($timeKey)
    {
        switch ($timeKey) {
            case 'actualMonthly':
                $date = new \DateTime();
                $date->sub(new \DateInterval('P30D'));
                return $date;
            case 'lastMonthly':
                $date = new \DateTime();
                $date->sub(new \DateInterval('P60D'));
                return $date;
            default:
                return null;
        }
    }

    protected function getFinishDateByTimeKey($timeKey)
    {
        switch ($timeKey) {
            case 'actualMonthly':
                $date = new \DateTime();
                $date->sub(new \DateInterval('P1D'));
                return $date;
                break;
            case 'lastMonthly':
                $date = new \DateTime();
                $date->sub(new \DateInterval('P31D'));
                return $date;
                break;
            default:
                return null;
        }
    }

    protected function getQuantity(OrderProduct $orderProduct)
    {
        return $orderProduct->getQuantity();
    }

    protected function initProductEntityCollection()
    {
        // TODO itemLog alapján következő X termék

        $this->productEntityCollection = new ArrayCollection();
        $products = $this->entityManager->getRepository('AppBundle:Product')->findAll();
        $this->AllItemProcessCount = count($products);
        if ($products) {
            $limitCounter = 0;
            foreach ($products as $product) {
                if ($limitCounter > $this->itemProcessLimit) {
                    break;
                }
                $this->productEntityCollection->add($product);
                $limitCounter++;
            }
        }
    }
}