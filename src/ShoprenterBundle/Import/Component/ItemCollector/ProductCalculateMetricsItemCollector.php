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

    /** @var array */
    protected $timeKeys = array('all', 'actualMonthly', 'lastMonthly');

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
            $statisticsCollection = $this->getProductStatisticsCollection($product);
            $statisticsArray = $statisticsCollection->toArray();
            if (!$statisticsArray) {
                foreach ($this->timeKeys as $timeKey) {
                    $statisticsArray[] = $this->newProductStatistics($product, $timeKey);
                }
            }
            $existTimeKey = array();
            foreach ($statisticsArray as $statistics) {
                $existTimeKey[] = $this->getTimeKey($statistics);
            }
            foreach ($this->timeKeys as $timeKey) {
                if (!in_array($timeKey, $existTimeKey)) {
                    $statisticsArray[] = $this->newProductStatistics($product, $timeKey);
                }
            }
            foreach ($statisticsArray as $statistics) {
                $isCheat = 0;
                $timeKey = $this->getTimeKey($statistics);
                $views = $this->getViews($statistics);
                $uniqueViews = $this->getUniqueViews($statistics);
                $orders = $this->getOrderCount($product, $timeKey);
                $uniqueOrders = $this->getUniqueOrderCount($product, $timeKey);
                if ($uniqueOrders > $uniqueViews) {
                    $uniqueViews = $uniqueOrders;
                    $isCheat = 1;
                }
                if ($orders > $views) {
                    $views = $orders;
                    $isCheat = 1;
                }
                $conversion = $this->getConversion($uniqueOrders, $uniqueViews);
                $total = $this->getOrderTotal($product, $timeKey);
                $score = ($conversion * 100) + ($uniqueOrders * 5) + ($uniqueViews * 2);
                $collectData[$timeKey] = array(
                    'object' => $statistics,
                    'calculatedViews' => $views,
                    'calculatedUniqueViews' => $uniqueViews,
                    'calculatedOrders' => $orders,
                    'calculatedUniqueOrders' => $uniqueOrders,
                    'calculatedConversion' => $conversion,
                    'calculatedTotal' => $total,
                    'calculatedScore' => $score,
                    'isCheat' => $isCheat,
                );
            }
            if ($collectData) {
                if (count($collectData) > 3) {
                    dump($collectData); die('HIBA');
                }
                foreach ($collectData as $timeKey => $values) {
                    $object = $values['object'];
                    $this->setCalculateMetricsObject($object, $values);
                }
            }
            $this->setProcessed($item, $key);
            $this->manageFlush();
        }
        $this->entityManager->flush();
    }

    protected function newProductStatistics(Product $product, $timeKey)
    {
        $object = new ProductStatistics();
        $object->setProduct($product);
        $object->setTimeKey($timeKey);
        return $object;
    }

    /**
     * @param Product $product
     * @param $timeKey
     * @return int
     */
    protected function getOrderCount(Product $product, $timeKey)
    {
        $productOrders = $product->getProductOrders();
        if (!$productOrders) {
            return 0;
        }
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
        if (!$productOrders) {
            return 0;
        }
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
     * @param Product $product
     * @param $timeKey
     * @return int
     */
    protected function getOrderTotal(Product $product, $timeKey)
    {
        $productOrders = $product->getProductOrders();
        if (!$productOrders) {
            return 0;
        }
        if (!$productOrders->count()) {
            return 0;
        }
        $startDate = $this->getStartDateByTimeKey($timeKey);
        $finishDate = $this->getFinishDateByTimeKey($timeKey);
        $counter = 0;
        foreach ($productOrders->toArray() as $orderProduct)
        {
            if (!$startDate || !$finishDate) {
                $counter += $this->getTotal($orderProduct);
                continue;
            }
            $orderDate = $this->getOrderProductOrderDate($orderProduct);
            if ($orderDate >= $startDate && $orderDate <= $finishDate) {
                $counter += $this->getTotal($orderProduct);
            }
        }
        return round($counter);
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

    /**
     * @param OrderProduct $orderProduct
     * @return int
     */
    protected function getTotal(OrderProduct $orderProduct)
    {
        return $orderProduct->getTotal();
    }

}