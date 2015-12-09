<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductStatistics;

class StarProducts
{
    /** @var string */
    protected $timeKey = 'actualMonthly';

    /** @var int */
    protected $limit = 100;

    /** @var Setting */
    protected $settingService;

    /** @var EntityManager */
    protected $entityManager;

    /** @var array */
    protected $products;

    /** @var array */
    protected $productsToReport = array();

    /** @var array */
    protected $productKeysByConversion = array();

    /** @var array */
    protected $productKeysByViews = array();

    /** @var array */
    protected $rowsToReport = array();

    /**
     * @param Setting $service
     */
    public function setSettingService(Setting $service)
    {
        $this->settingService = $service;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->reset();
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getReport()
    {
        if ($this->rowsToReport) {
            return $this->rowsToReport;
        }
        $this->loadProductList();
        $this->loadStatistics();
        $this->createRows();
        return $this->rowsToReport;
    }

    protected function loadProductList()
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->andWhere('ps.uniqueViews > 0')
            ->setParameter('timeKey', $this->timeKey)
            ->orderBy('ps.uniqueViews', 'DESC')
            ->setMaxResults(1000);
        $this->products = $query->getQuery()->getResult();
    }

    protected function loadStatistics()
    {
        foreach ($this->products as $product) {
            $statisticsEntities = $this->getProductStatistics($product);
            $this->loadOneProductStatistics($statisticsEntities, $product);
        }
    }

    protected function createRows()
    {
        arsort($this->productKeysByConversion);

        $i = 0;
        foreach ($this->productKeysByConversion as $key => $conversion) {
            if (!$conversion) {
                continue;
            }
            foreach ($this->productsToReport[$key] as $value) {
                $this->rowsToReport[$i][] = $value;
            }
            $i++;
            if ($i >= $this->limit) {
                break;
            }
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
        $orderCount = $this->getOrderCount($product->getOuterId());
        $conversion = 0;
        if ($orderCount) {
            if ($orderCount > $views) {
                $views = $orderCount;
            }
            $conversion = round($orderCount / $views * 100, 2);
        }

        $picture = '';
        if ($product->getPicture()) {
            $picture = $this->settingService->get('picture_url') . $product->getPicture();
        }
        $name = $product->getName();
        $sku = $product->getSku();
        $availableDateTime = $product->getAvailableDate();
        $availableDate = $availableDateTime->format('d.m.Y.');

        $this->productsToReport[] = array(
            'picture' => $picture,
            'name' => $name,
            'sku' => $sku,
            'availableDate' => $availableDate,
            'orderCount' => $orderCount,
            'conversion' => $conversion,
            'allTimeViews'=> $allViews,
            'views' => $views,
        );
        $this->productKeysByConversion[] = $conversion;
        $this->productKeysByViews[] = $views;
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
        return $statistics->getUniqueViews();
    }

    /**
     * @param $productId
     * @return mixed
     */
    protected function getOrderCount($productId)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P30D'));
        $dateFilter = $date->format('Y-m-d');
        $query = $this->entityManager->createQueryBuilder();
        $query->select('op')
            ->from('AppBundle:OrderProduct', 'op')
            ->where('op.productOuterId = :productId')
            ->andWhere('op.orderDate >= :orderDate')
            ->setParameter('productId', $productId)
            ->setParameter('orderDate', $dateFilter);
        $ops = $query->getQuery()->getResult();
        if (!$ops) {
            return 0;
        }
        return count($ops);
    }

    protected function reset()
    {
        $this->products = array();
        $this->productsToReport = array();
        $this->productKeysByConversion = array();
        $this->productKeysByViews = array();
        $this->rowsToReport = array();
    }
}