<?php
namespace AppBundle\Report;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductStatistics;

class ProductReport extends Report
{
    /** @var int */
    protected $limit = 100;

    /** @var int */
    protected $avgUniqueViews;

    /** @var int */
    protected $avgConversion;

    /** @var array */
    protected $keysByConversion = array();

    /** @var array */
    protected $keysByView = array();

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

        $views = 0;
        $allViews = 0;
        if ($actualStatisticsData) {
            $views = $this->getUniqueViews($actualStatisticsData);
        }
        if ($allTimeStatisticsData) {
            $allViews = $this->getUniqueViews($allTimeStatisticsData);
        }
        $orderCount = $this->getUniqueOrders($actualStatisticsData);
        $conversion = $this->getConversion($actualStatisticsData);

        $picture = '';
        if ($product->getPicture()) {
            $picture = $this->settingService->get('picture_url') . $product->getPicture();
        }
        $name = $product->getName();
        $sku = $product->getSku();
        $availableDateTime = $product->getAvailableDate();
        $availableDate = $availableDateTime->format('d.m.Y.');
        $score = $this->getScore($actualStatisticsData);

        $this->collectedData[] = array(
            'picture' => $picture,
            'name' => $name,
            'sku' => $sku,
            'availableDate' => $availableDate,
            'score' => $score,
            'orderCount' => $orderCount,
            'conversion' => $conversion,
            'allTimeViews'=> $allViews,
            'views' => $views,
        );
        $this->keysByConversion[] = $conversion;
        $this->keysByView[] = $views;
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
}