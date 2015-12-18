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
use AppBundle\Service\BaseStatistics;
use AppBundle\Service\XlsExport;

/**
 * Ez jelenleg csak arra szolgál, hogy elérhessük a belőtt boltok termék riportjait,
 * a riportok algoritmusanainak finomítása végett.
 *
 * Ha ez élesre felkerül, elérését jelszóval kell védeni.
 *
 * Több boltot (user-t) kezel (jelenleg tpl-ben manuálisan kell felvenne egy-egy új boltot)
 * A/B verziót kezel riportonként
 * Kiexportálhatóak XLS-be azon adatok, amelyeket használnak a riportok.
 */
class ProductPlacementTester extends Controller
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

    /** @var BaseStatistics */
    protected $baseStatistics;

    /** @var string */
    protected $exportFileName = '';

    /** @var string */
    protected $exportUrl = '';

    /**
     * @Route("/product_placement_tester", name="Product Placement Tester")
     */
    public function indexAction(Request $request)
    {
        if ($request->query->get('user_id')) {
            $this->userId = $request->query->get('user_id');
        }
        $this->entityManager = $this->getDoctrine()->getManager('customer' . $this->userId);
        $this->settingService = $this->get('setting');
        $this->settingService->setEntityManager($this->entityManager);

        $this->baseStatistics = $this->get('BaseStatistics');
        $this->baseStatistics->setUserId($this->userId);
        $this->baseStatistics->setEntityManager($this->entityManager);
        $this->baseStatistics->setTimeKey($this->timeKey);

        $accuracy = round(100 - (($this->baseStatistics->get('CheatCount') + ($this->baseStatistics->get('NullStatisticsCount') / 2)) / $this->baseStatistics->get('ProductCount') * 100));

        if ($request->query->get('export')) {
            $time = new \DateTime();
            $timestamp = $time->format('YmdHis');
            $this->exportFileName = 'export-' . $this->userId . '-' . $timestamp;
            $this->exportUrl = 'downloads/' . $this->exportFileName . '.xlsx';
            $this->export();
        }

        $i = 0;

        $reports[$i]['A'] = array(
            'version' => 'A',
            'reportId' => 'star-products',
            'reportName' => 'Star Products',
            'reportArray' => $this->getReportBlockRows('StarProducts', 'A'),
            'reportConditions' => array('Conversion > 0', 'TOP 100 Unique Views'),
            'reportOrders' => array('Conversion decreasing', 'Unique Views decreasing'),
        );

        $reports[$i]['B'] = array(
            'version' => 'B',
            'reportId' => 'star-products',
            'reportName' => 'Star Products',
            'reportArray' => $this->getReportBlockRows('StarProducts', 'B'),
            'reportConditions' => array('Unique Views > {Avg Unique Views * 3}', 'Conversion > {Avg Conversion * 3}'),
            'reportOrders' => array('Score ( {Conversion * 10} + {Unique Orders * 5} + {Unique Views * 2} ) decreasing', 'Conversion decreasing', 'Unique Views decreasing'),
        );
        $i++;


        $reports[$i]['A'] = array(
            'version' => 'A',
            'reportId' => 'potentials',
            'reportName' => 'Potentials',
            'reportArray' => $this->getReportBlockRows('Potentials', 'A'),
            'reportConditions' => array('Conversion > 0', 'LAST 100 Unique Views'),
            'reportOrders' => array('Conversion decreasing', 'Unique Views ascending'),
        );

        $reports[$i]['B'] = array(
            'version' => 'B',
            'reportId' => 'potentials',
            'reportName' => 'Potentials',
            'reportArray' => $this->getReportBlockRows('Potentials', 'B'),
            'reportConditions' => array('Unique Views <= {Avg Unique Views * 3}', 'Conversion > 0'),
            'reportOrders' => array('Conversion decreasing', 'Unique Views decreasing'),
        );
        $i++;


        $reports[$i]['A'] = array(
            'version' => 'A',
            'reportId' => 'black-horses',
            'reportName' => 'Black Horses',
            'reportArray' => $this->getReportBlockRows('BlackHorses', 'A'),
            'reportConditions' => array('TOP 100 Unique Views'),
            'reportOrders' => array('Conversion ascending', 'Unique Views decreasing'),
        );

        $reports[$i]['B'] = array(
            'version' => 'B',
            'reportId' => 'black-horses',
            'reportName' => 'Black Horses',
            'reportArray' => $this->getReportBlockRows('BlackHorses', 'B'),
            'reportConditions' => array('Unique Views > {Avg Unique Views * 3}', 'Conversion <= Avg Conversion'),
            'reportOrders' => array('Conversion ascending', 'Unique Views decreasing'),
        );
        $i++;


        $reports[$i]['A'] = array(
            'version' => 'A',
            'reportId' => 'end-of-list',
            'reportName' => 'End Of List',
            'reportArray' => $this->getReportBlockRows('EndOfList', 'A'),
            'reportConditions' => array('Conversion = 0'),
            'reportOrders' => array('Unique Views ascending', 'Views ascending', 'Available Date ascending'),
        );

        $reports[$i]['B'] = array(
            'version' => 'B',
            'reportId' => 'end-of-list',
            'reportName' => 'End Of List',
            'reportArray' => $this->getReportBlockRows('EndOfList', 'B'),
            'reportConditions' => array('LAST 100 Unique Views at all times', 'Unique Views at all times > 0', 'Unique Views at all times <= {Avg Unique Views * 3}', 'Conversion at all times <= Avg Conversion'),
            'reportOrders' => array('Conversion ascending', 'Unique Views ascending', 'Views ascending', 'Available Date ascending'),
        );
        $i++;


        $reports[$i]['A'] = array(
            'version' => 'A',
            'reportId' => 'difficult-cases',
            'reportName' => 'Difficult Cases',
            'reportArray' => $this->getReportBlockRows('DifficultCases', 'A'),
            'reportConditions' => array('LAST 100 Unique Views'),
            'reportOrders' => array('Conversion ascending', 'Unique Views ascending'),
        );

        $reports[$i]['B'] = array(
            'version' => 'B',
            'reportId' => 'difficult-cases',
            'reportName' => 'Difficult Cases',
            'reportArray' => $this->getReportBlockRows('DifficultCases', 'B'),
            'reportConditions' => array('Unique Views > Avg Unique Views', 'Unique Views <= {Avg Unique Views * 3}', 'Conversion <= Avg Conversion'),
            'reportOrders' => array('Conversion ascending', 'Unique Views ascending'),
        );
        $i++;


        return $this->render('AppBundle:tester:product_placement.html.twig', array(
            'reportList' => $reports,
            'lastUpdate' => $this->getLastUpdateTime(),
            'avgUniqueViews' => $this->baseStatistics->get('AvgUniqueViews'),
            'avgConversion' => $this->baseStatistics->get('AvgConversion'),
            'avgUniqueViewsAtInteractiveProducts' => $this->baseStatistics->get('AvgUniqueViewsAtInteractiveProducts'),
            'avgConversionAtInteractiveProducts' => $this->baseStatistics->get('AvgConversionAtInteractiveProducts'),
            'lastAvgUniqueViews' => $this->baseStatistics->get('AvgUniqueViews', 'lastMonthly'),
            'lastAvgConversion' => $this->baseStatistics->get('AvgConversion', 'lastMonthly'),
            'lastAvgUniqueViewsAtInteractiveProducts' => $this->baseStatistics->get('AvgUniqueViewsAtInteractiveProducts', 'lastMonthly'),
            'lastAvgConversionAtInteractiveProducts' => $this->baseStatistics->get('AvgConversionAtInteractiveProducts', 'lastMonthly'),
            'cheatCount' => $this->baseStatistics->get('CheatCount'),
            'productCount' => $this->baseStatistics->get('ProductCount'),
            'nullStatisticsCount' => $this->baseStatistics->get('NullStatisticsCount'),
            'accuracy' => $accuracy,
            'exportUrl' => $this->exportUrl,
        ));
    }

    protected function getReportBlockRows($reportName, $version)
    {
        $this->$reportName = $this->get('Report_' . $reportName);
        $this->$reportName->setSettingService($this->settingService);
        $this->$reportName->setEntityManager($this->entityManager);
        $this->$reportName->setAvgUniqueViews($this->baseStatistics->get('AvgUniqueViews'));
        $this->$reportName->setAvgConversion($this->baseStatistics->get('AvgConversion'));
        $this->$reportName->setProductCount($this->baseStatistics->get('ProductCount'));
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
        $XlsExport->export($this->exportFileName, $head, $data);
    }
}
