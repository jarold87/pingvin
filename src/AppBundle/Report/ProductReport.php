<?php
namespace AppBundle\Report;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductStatistics;

class ProductReport extends Report
{
    /** @var int */
    protected $maxLimit = 100;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $avgUniqueViews;

    /** @var int */
    protected $avgConversion;

    /** @var int */
    protected $productCount;

    /**
     * @param $value
     */
    public function setAvgUniqueViews($value)
    {
        $this->avgUniqueViews = $value;
    }

    /**
     * @param $value
     */
    public function setAvgConversion($value)
    {
        $this->avgConversion = $value;
    }

    /**
     * @param $value
     */
    public function setProductCount($value)
    {
        $this->productCount = $value;
    }

    protected function calculateLimit()
    {
        $this->limit = $this->maxLimit;
        if (($this->productCount * 0.1) < $this->maxLimit) {
            $this->limit = round($this->productCount * 0.1);
        }
    }

    protected function loadStatistics()
    {
        foreach ($this->list as $product) {
            $statisticsEntities = $this->getProductStatistics($product);
            $this->loadOneProductStatistics($statisticsEntities, $product);
        }
    }

    /**
     * @param array $statisticsEntities
     * @param Product $product
     */
    protected function loadOneProductStatistics(array $statisticsEntities, Product $product)
    {
        $productId = $product->getProductId();
        $actualStatisticsData = $statisticsEntities[0];
        $allTimeStatisticsData = $this->getAllTimeProductsStatisticsByProductId($productId);

        $allViews = 0;
        $allOrders = 0;
        $allConversion = 0;
        $allTotal = 0;
        $views = $this->getUniqueViews($actualStatisticsData);
        if ($allTimeStatisticsData) {
            $allViews = $this->getUniqueViews($allTimeStatisticsData);
        }
        $orderCount = $this->getUniqueOrders($actualStatisticsData);
        if ($allTimeStatisticsData) {
            $allOrders = $this->getUniqueViews($allTimeStatisticsData);
        }
        $conversion = $this->getConversion($actualStatisticsData);
        if ($allTimeStatisticsData) {
            $allConversion = $this->getConversion($allTimeStatisticsData);
        }
        $total = $this->getTotal($actualStatisticsData);
        if ($allTimeStatisticsData) {
            $allTotal = $this->getTotal($allTimeStatisticsData);
        }

        $picture = '';
        if ($product->getPicture()) {
            $picture = $this->settingService->get('picture_url') . $product->getPicture();
        }
        $name = $product->getName();
        $sku = $product->getSku();
        $availableDateTime = $product->getAvailableDate();
        $availableDate = $availableDateTime->format('m.Y.');
        $score = $this->getScore($actualStatisticsData);

        $this->collectedData[] = array(
            'picture' => $picture,
            'name' => $name,
            'sku' => $sku,
            'availableDate' => $availableDate,
            'score' => $score,
            'orderCount' => $orderCount,
            'conversion' => $conversion,
            'total' => $total,
            'views' => $views,
            'allTimeViews'=> $allViews,
            'allTimeOrderCount'=> $allOrders,
            'allTimeConversion'=> $allConversion,
            'allTimeTotal' => $allTotal,
        );
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductStatistics(Product $product)
    {
        return $product->getProductStatistics()->toArray();
    }

    /**
     * @param $productId
     * @return null|object
     */
    protected function getAllTimeProductsStatisticsByProductId($productId)
    {
        $allTimeStatisticsData = $this->entityManager->getRepository('AppBundle:ProductStatistics')->findOneBy(
            array(
                'productId' => $productId,
                'timeKey' => 'all',
            )
        );
        if (!$allTimeStatisticsData) {
            return null;
        }
        return $allTimeStatisticsData;
    }

    /**
     * @param ProductStatistics $statistics
     * @return int
     */
    protected function getUniqueViews(ProductStatistics $statistics)
    {
        return $statistics->getCalculatedUniqueViews();
    }

    /**
     * @param ProductStatistics $statistics
     * @return int
     */
    protected function getUniqueOrders(ProductStatistics $statistics)
    {
        return $statistics->getCalculatedUniqueOrders();
    }

    /**
     * @param ProductStatistics $statistics
     * @return int
     */
    protected function getConversion(ProductStatistics $statistics)
    {
        return $statistics->getCalculatedConversion();
    }

    /**
     * @param ProductStatistics $statistics
     * @return int
     */
    protected function getScore(ProductStatistics $statistics)
    {
        return $statistics->getCalculatedScore();
    }

    /**
     * @param ProductStatistics $statistics
     * @return int
     */
    protected function getTotal(ProductStatistics $statistics)
    {
        return $statistics->getCalculatedTotal();
    }
}