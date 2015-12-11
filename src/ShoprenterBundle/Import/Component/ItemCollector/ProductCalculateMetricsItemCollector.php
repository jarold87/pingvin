<?php

namespace ShoprenterBundle\Import\Component\ItemCollector;

use CronBundle\Import\Component\ItemCollector\ItemCollectorByLoadFromUserDatabase;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductStatistics;
use AppBundle\Entity\OrderProduct;

class ProductCalculateMetricsItemCollector extends ItemCollectorByLoadFromUserDatabase
{
    /** @var string */
    protected $processEntityName = 'ImportItemProcess';

    /** @var string */
    protected $sourceEntityName;

    public function collect()
    {
        if (!$this->itemProcessCollection->count()) {
            return;
        }
        $items = $this->itemProcessCollection->toArray();
        $this->collectItemsCalculateMetrics($items);
        parent::collect();
    }

    /**
     * @param $items
     */
    protected function collectItemsCalculateMetrics($items)
    {
        foreach ($items as $key => $item) {
            if (!$this->isInLimits()) {
                break;
            }
            $collectData = array();
            $product = $this->searchEntity($item);
            if (!$product) {
                $this->addError('Missing entity!');
                return;
            }
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
                        'object' => $statistics,
                        'views' => $views,
                        'uniqueViews' => $uniqueViews,
                        'orders' => $orders,
                        'uniqueOrders' => $uniqueOrders,
                        'conversion' => $conversion,
                    );
                    $collectData[$productId][$timeKey] = $statisticsArray;
                }
            }
            if ($collectData) {
                $productIds = array_keys($collectData);
                foreach ($productIds as $productId) {
                    foreach ($collectData[$productId] as $timeKey => $values) {
                        $object = $values['object'];
                        $this->setCalculateMetricsObject($object, $values);
                    }
                }
            }
            $this->setProcessed($item, $key);
            $this->manageFlush();
        }
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

    /**
     * @param $timeKey
     * @return \DateTime|null
     */
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

    /**
     * @param $timeKey
     * @return \DateTime|null
     */
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

    /**
     * @param $orderCount
     * @param $views
     * @return float|int
     */
    protected function getConversion($orderCount, $views)
    {
        if ($orderCount) {
            return round($orderCount / $views * 100, 2);
        }
        return 0;
    }

    /**
     * @param OrderProduct $orderProduct
     * @return \DateTime
     */
    protected function getOrderProductOrderDate(OrderProduct $orderProduct)
    {
        return $orderProduct->getOrderDate();
    }

    /**
     * @param Product $product
     * @return \Doctrine\Common\Collections\Collection
     */
    protected function getProductStatisticsCollection(Product $product)
    {
        return $product->getProductStatistics();
    }

    /**
     * @param Product $product
     * @return int
     */
    protected function getProductId(Product $product)
    {
        return $product->getProductId();
    }

    /**
     * @param ProductStatistics $productStatistics
     * @return string
     */
    protected function getTimeKey(ProductStatistics $productStatistics)
    {
        return $productStatistics->getTimeKey();
    }

    /**
     * @param ProductStatistics $productStatistics
     * @return int
     */
    protected function getViews(ProductStatistics $productStatistics)
    {
        return $productStatistics->getViews();
    }

    /**
     * @param ProductStatistics $productStatistics
     * @return int
     */
    protected function getUniqueViews(ProductStatistics $productStatistics)
    {
        return $productStatistics->getUniqueViews();
    }

    /**
     * @param OrderProduct $orderProduct
     * @return int
     */
    protected function getQuantity(OrderProduct $orderProduct)
    {
        return $orderProduct->getQuantity();
    }

}