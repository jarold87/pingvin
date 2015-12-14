<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\ImportScheduleLog;
use AppBundle\Service\Setting;
use AppBundle\Service\StarProducts;
use AppBundle\Service\BlackHorses;


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

        return $this->render('AppBundle::product_placement.html.twig', array(
            'StarProducts' => $this->getReportBlockRows('StarProducts'),
            'BlackHorses' => $this->getReportBlockRows('BlackHorses'),
            'EndOfList' => $this->getReportBlockRows('EndOfList'),
            'Potentials' => $this->getReportBlockRows('Potentials'),
            //'DifficultCases' => $this->getReportBlockRows('DifficultCases'),
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
        $query->select('avg(ps.uniqueViews)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', $this->timeKey)
            ->getQuery();
        $avgScore = $query->getQuery()->getResult();
        $value = round($avgScore[0][1], 0);
        $this->avgUniqueViews = $value;

        $query = $this->entityManager->createQueryBuilder();
        $query->select('avg(ps.conversion)')
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
        $value = round($countScore[0][1], 2);
        $this->cheatCount = $value;

        $query = $this->entityManager->createQueryBuilder();
        $query->select('count(p.productId)')
            ->from('AppBundle:Product', 'p')
            ->where('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->getQuery();
        $countScore = $query->getQuery()->getResult();
        $value = round($countScore[0][1], 2);
        $this->productCount = $value;
    }

    protected function getReportBlockRows($reportName)
    {
        $this->$reportName = $this->get('Report_' . $reportName);
        $this->$reportName->setSettingService($this->settingService);
        $this->$reportName->setEntityManager($this->entityManager);
        $this->$reportName->setAvgUniqueViews($this->avgUniqueViews);
        $this->$reportName->setAvgConversion($this->avgConversion);
        $this->$reportName->setTimeKey($this->timeKey);
        return $this->$reportName->getReport();
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
}
