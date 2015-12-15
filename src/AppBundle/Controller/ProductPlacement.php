<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\ImportScheduleLog;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductStatistics;
use AppBundle\Service\Setting;
use AppBundle\Service\StarProducts;
use AppBundle\Service\BlackHorses;
use AppBundle\Service\XlsExport;


class ProductPlacement extends Controller
{
    /** @var int */
    protected $userId = 1;

    /** @var string */
    protected $timeKey = 'actualMonthly';

    /** @var EntityManager */
    protected $entityManager;

    /** @var EntityManager */
    protected $globalEntityManager;

    /** @var Setting */
    protected $settingService;

    /** @var StarProducts */
    protected $StarProducts;

    /** @var BlackHorses */
    protected $BlackHorses;

    /** @var int */
    protected $avgUniqueViews;

    /** @var int */
    protected $avgConversion;

    /** @var int */
    protected $cheatCount;

    /** @var int */
    protected $productCount;

    /**
     * @Route("/product_placement", name="Product Placement")
     */
    public function indexAction(Request $request)
    {
        if ($request->query->get('user_id')) {
            $this->userId = $request->query->get('user_id');
        }
        $this->entityManager = $this->getDoctrine()->getManager('customer' . $this->userId);
        $this->settingService = $this->get('setting');
        $this->settingService->setEntityManager($this->entityManager);

        $this->loadGlobalData();

        $accuracy = 100;
        if ($this->cheatCount) {
            $accuracy = round(100 - ($this->cheatCount / $this->productCount * 100));
        }

        //$this->export();

        $reports[0]['A'] = array(
            'version' => 'A',
            'reportId' => 'star-products',
            'reportName' => 'Star Products',
            'reportArray' => $this->getReportBlockRows('StarProducts', 'A'),
            'reportConditions' => array('TOP 100 Unique Views'),
            'reportOrders' => array('Conversion decreasing', 'Unique Views decreasing'),
        );

        $reports[0]['B'] = array(
            'version' => 'B',
            'reportId' => 'star-products',
            'reportName' => 'Star Products',
            'reportArray' => $this->getReportBlockRows('StarProducts', 'B'),
            'reportConditions' => array('Unique Views > {Avg Unique Views * 3}', 'Conversion > {Avg Conversion * 3}'),
            'reportOrders' => array('Score ( {Conversion * 10} + {Unique Orders * 5} + {Unique Views * 2} ) decreasing', 'Conversion decreasing', 'Unique Views decreasing'),
        );

        $reports[1]['A'] = array(
            'version' => 'A',
            'reportId' => 'black-horses',
            'reportName' => 'Black Horses',
            'reportArray' => $this->getReportBlockRows('BlackHorses', 'A'),
            'reportConditions' => array('TOP 100 Unique Views'),
            'reportOrders' => array('Conversion ascending', 'Unique Views decreasing'),
        );

        $reports[1]['B'] = array(
            'version' => 'B',
            'reportId' => 'black-horses',
            'reportName' => 'Black Horses',
            'reportArray' => $this->getReportBlockRows('BlackHorses', 'B'),
            'reportConditions' => array('Unique Views > {Avg Unique Views * 3}', 'Conversion <= Avg Conversion'),
            'reportOrders' => array('Conversion ascending', 'Unique Views decreasing'),
        );

        $reports[2]['A'] = array(
            'version' => 'A',
            'reportId' => 'end-of-list',
            'reportName' => 'End Of List',
            'reportArray' => $this->getReportBlockRows('EndOfList', 'A'),
            'reportConditions' => array('Conversion = 0'),
            'reportOrders' => array('Unique Views ascending', 'Views ascending', 'Available Date ascending'),
        );

        $reports[2]['B'] = array(
            'version' => 'B',
            'reportId' => 'end-of-list',
            'reportName' => 'End Of List',
            'reportArray' => $this->getReportBlockRows('EndOfList', 'B'),
            'reportConditions' => array('Unique Views > 0', 'Unique Views <= {Avg Unique Views * 3}', 'Conversion <= Avg Conversion'),
            'reportOrders' => array('Conversion ascending', 'Unique Views decreasing', 'Views decreasing', 'Available Date ascending'),
        );

        $reports[3]['A'] = array(
            'version' => 'A',
            'reportId' => 'potentials',
            'reportName' => 'Potentials',
            'reportArray' => $this->getReportBlockRows('Potentials', 'A'),
            'reportConditions' => array('Conversion > 0', 'LAST 100 Unique Views'),
            'reportOrders' => array('Conversion decreasing', 'Unique Views ascending', 'TOP 100 Unique Views'),
        );

        $reports[3]['B'] = array(
            'version' => 'B',
            'reportId' => 'potentials',
            'reportName' => 'Potentials',
            'reportArray' => $this->getReportBlockRows('Potentials', 'B'),
            'reportConditions' => array('Unique Views <= {Avg Unique Views * 3}', 'Conversion > 0'),
            'reportOrders' => array('Conversion decreasing', 'Unique Views decreasing'),
        );

        return $this->render('AppBundle::product_placement.html.twig', array(
            'reportList' => $reports,
            'lastUpdate' => $this->getLastUpdateTime(),
            'avgUniqueViews' => $this->avgUniqueViews,
            'avgConversion' => $this->avgConversion,
            'cheatCount' => $this->cheatCount,
            'productCount' => $this->productCount,
            'accuracy' => $accuracy
        ));
    }


    protected function loadGlobalData()
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('avg(ps.calculatedUniqueViews)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->setParameter('timeKey', $this->timeKey)
            ->getQuery();
        $avgScore = $query->getQuery()->getResult();
        $value = round($avgScore[0][1], 2);
        $this->avgUniqueViews = $value;

        $query = $this->entityManager->createQueryBuilder();
        $query->select('avg(ps.calculatedConversion)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', $this->timeKey)
            ->getQuery();
        $avgScore = $query->getQuery()->getResult();
        $value = round($avgScore[0][1], 2);
        $this->avgConversion = $value;

        $query = $this->entityManager->createQueryBuilder();
        $query->select('count(ps.productStatisticsId)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('ps.isCheat = 1')
            ->setParameter('timeKey', $this->timeKey)
            ->getQuery();
        $countScore = $query->getQuery()->getResult();
        $value = round($countScore[0][1]);
        $this->cheatCount = $value;

        $query = $this->entityManager->createQueryBuilder();
        $query->select('count(p.productId)')
            ->from('AppBundle:Product', 'p')
            ->where('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->getQuery();
        $countScore = $query->getQuery()->getResult();
        $value = round($countScore[0][1]);
        $this->productCount = $value;
    }

    protected function getReportBlockRows($reportName, $version)
    {
        $this->$reportName = $this->get('Report_' . $reportName);
        $this->$reportName->setSettingService($this->settingService);
        $this->$reportName->setEntityManager($this->entityManager);
        $this->$reportName->setAvgUniqueViews($this->avgUniqueViews);
        $this->$reportName->setAvgConversion($this->avgConversion);
        $this->$reportName->setTimeKey($this->timeKey);
        return $this->$reportName->getReport($version);
    }

    protected function getLastUpdateTime()
    {
        $this->globalEntityManager = $this->getDoctrine()->getManager('global');
        $query = $this->globalEntityManager->createQueryBuilder();
        $query->select('l')
            ->from('AppBundle:ImportScheduleLog', 'l')
            ->where('l.userId = :userId')
            ->setParameter('userId', $this->userId)
            ->orderBy('l.lastFinishedImportDate', 'DESC')
            ->setMaxResults(1);
        $lasts = $query->getQuery()->getResult();
        /** @var ImportScheduleLog $last */
        $last = $lasts[0];
        $date = $last->getLastFinishedImportDate();
        return $date->format('l, d.m. H:i A');
    }

    protected function export()
    {
        $head = array(
            'name',
            'sku',
            'available date',
            'unique orders',
            'conversion',
            'unique views',
            'score',
        );
        $query = $this->entityManager->createQueryBuilder();
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', $this->timeKey)
            ->addOrderBy('ps.calculatedScore', 'DESC')
            ->addOrderBy('ps.calculatedConversion', 'DESC')
            ->addOrderBy('ps.calculatedUniqueViews', 'DESC')
            ->addOrderBy('ps.calculatedViews', 'DESC')
            ->setMaxResults(10000);
        $list = $query->getQuery()->getResult();
        if (!$list) {
            return;
        }
        $data = array();
        foreach ($list as $item) {
            /** @var Product $p */
            $p = $item;
            $psa = $p->getProductStatistics();
            /** @var ProductStatistics $ps */
            $ps = $psa[0];
            $data[] = array(
                $p->getName(),
                $p->getSku(),
                $p->getAvailableDate(),
                $ps->getCalculatedUniqueOrders(),
                $ps->getCalculatedConversion(),
                $ps->getCalculatedUniqueViews(),
                $ps->getCalculatedScore(),
            );
        }

        /** @var XlsExport $XlsExport */
        $XlsExport = $this->get('XlsExport');
        $XlsExport->export('export', $head, $data);
    }
}
