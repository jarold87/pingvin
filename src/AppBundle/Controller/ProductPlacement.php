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
    protected $minimumUniqueViews;

    /** @var int */
    protected $minimumConversion;

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

        return $this->render('AppBundle::product_placement.html.twig', array(
            'StarProducts' => $this->getReportBlockRows('StarProducts'),
            'BlackHorses' => $this->getReportBlockRows('BlackHorses'),
            'lastUpdate' => $this->getLastUpdateTime(),
            'minimumUniqueViews' => $this->minimumUniqueViews,
            'minimumConversion' => $this->minimumConversion,
        ));
    }


    protected function loadGlobalData()
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('avg(ps.uniqueViews)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->setParameter('timeKey', $this->timeKey)
            ->getQuery();
        $avgScore = $query->getQuery()->getResult();
        $value = round($avgScore[0][1], 0);
        $this->minimumUniqueViews = $value;

        $query = $this->entityManager->createQueryBuilder();
        $query->select('avg(ps.conversion)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->setParameter('timeKey', $this->timeKey)
            ->getQuery();
        $avgScore = $query->getQuery()->getResult();
        $value = round($avgScore[0][1], 2);
        $this->minimumConversion = $value;
    }

    protected function getReportBlockRows($reportName)
    {
        $this->$reportName = $this->get('Report_' . $reportName);
        $this->$reportName->setSettingService($this->settingService);
        $this->$reportName->setEntityManager($this->entityManager);
        $this->$reportName->setMinimumUniqueViews($this->minimumUniqueViews);
        $this->$reportName->setMinimumConversion($this->minimumConversion);
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
