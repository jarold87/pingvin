<?php

namespace TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Setting;

class StatisticsTest extends Controller
{
    /** @var EntityManager */
    protected $entityManager;

    /**
     * @Route("/stat_test", name="stat_test")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->entityManager = $this->getDoctrine()->getManager();

        $query = $this->entityManager->createQueryBuilder();
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->setParameter('timeKey', 'actualMonthly')
            ->orderBy('ps.uniqueViews', 'DESC')
            ->setMaxResults(100);
        $products = $query->getQuery()->getResult();
        $outProducts = array();
        $productKeysByConversion = array();
        $productKeysByViews = array();
        foreach ($products as $product) {
            $statistics = $product->getProductStatistics()->toArray();
            $actualStatisticsData = null;
            $allStatisticsData = null;
            foreach ($statistics as $data) {
                if ($data->getTimeKey() == 'actualMonthly') {
                    $actualStatisticsData = $data;
                }
            }
            $allStatisticsData = $this->entityManager->getRepository('AppBundle:ProductStatistics')->findOneBy(
                array(
                    'productId' => $product->getProductId(),
                    'timeKey' => 'all',
                )
            );
            $views = 0;
            $allViews = 0;
            $name = $product->getName();
            $sku = $product->getSku();
            if ($actualStatisticsData) {
                $views = $actualStatisticsData->getUniqueViews();
            }
            if ($allStatisticsData) {
                $allViews = $allStatisticsData->getUniqueViews();
            }
            $orderCount = $this->getOrderCount($product->getOuterId());
            $conversion = 0;
            if ($orderCount) {
                $conversion = round($orderCount / $views * 100, 2);
            }
            $outProducts[] = array($name, $sku, $views, $allViews, $orderCount, $conversion);
            $productKeysByConversion[] = $conversion;
            $productKeysByViews[] = $views;
        }
        asort($productKeysByConversion);
        arsort($productKeysByViews);
        echo '<table border="1" cellpadding="5">';
        echo '<tr><td></td><td></td><td>Egyedi látogató</td><td>Egyedi látogató mindenidők</td><td>Egyedi rendelés</td><td>Konverzió (%)</td></tr>';
        foreach ($productKeysByViews as $key => $views) {
            if ($outProducts[$key][5]) {
                continue;
            }
            echo '<tr>';
            foreach ($outProducts[$key] as $value) {
                echo '<td>' . $value . '</td>';
            }
            echo '</tr>';
        }
        foreach ($productKeysByConversion as $key => $conversion) {
            if (!$conversion) {
                continue;
            }
            echo '<tr>';
            foreach ($outProducts[$key] as $value) {
                echo '<td>' . $value . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';

        return $this->render('CronBundle::message.html.twig', array(
            'message' => '...',
        ));
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
}