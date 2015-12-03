<?php

namespace TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GoogleApiBundle\Services\Analytics;
use \Google_Client as GoogleClient;
use GuzzleHttp\Client as GuzzleHttpClient;


class gaTest extends Controller
{
    /** @var GoogleClient */
    protected $client;

    /** @var Analytics */
    protected $analyticsService;

    /** @var string */
    protected $profileId = '19036183';

    /** @var string */
    protected $serviceId = '';

    /**
     * @Route("/gatest", name="front_ga1")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->init();
        if (!$this->serviceId) {
            die('Hiányzó serviceId!');
        }
        $metrics = array(
            'ga:entrances',
            'ga:pageviews',
            'ga:uniquePageviews',
            'ga:exitRate'
        );
        $optParams = array(
            'dimensions' => 'ga:pagePath',
            'sort' => '-ga:uniquePageviews',
            'max-results' => '100000'
        );

        $response = $this->analyticsService->data_ga->get(
            'ga:' . $this->serviceId,
            '2015-01-01',
            '2015-11-27',
            join(',', $metrics),
            $optParams,
            []
        );

        $return = $this->convertToDataTable($response);
        echo $return;

        return $this->render('CronBundle::message.html.twig', array(
            'message' => '...',
        ));
    }

    protected function init()
    {
        $this->initService();
        $this->loadProfileId();
    }

    protected function initService()
    {
        $this->analyticsService = $this->get('GoogleAnalyticsService');
    }

    protected function loadProfileId()
    {
        $id = $this->profileId;
        $accounts = $this->analyticsService->management_accounts->listManagementAccounts();
        $accountId = '';
        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();
            foreach ($items as $item) {
                if ($item->getId() == $id) {
                    $accountId = $item->getId();
                }
            }
            if ($accountId) {
                $properties = $this->analyticsService->management_webproperties
                    ->listManagementWebproperties($accountId);
                if (count($properties->getItems()) > 0) {
                    $items = $properties->getItems();
                    $firstPropertyId = $items[0]->getId();
                    $profiles = $this->analyticsService->management_profiles
                        ->listManagementProfiles($accountId, $firstPropertyId);
                    if (count($profiles->getItems()) > 0) {
                        $items = $profiles->getItems();
                        $this->serviceId = $items[0]->getId();
                    }
                }
            }
        }
    }

    protected function convertToDataTable($results)
    {
        $table = '';
        if (count($results->getRows()) > 0) {
            $table .= '<table>';

            // Print headers.
            $table .= '<tr>';

            foreach ($results->getColumnHeaders() as $header) {
                $table .= '<th>' . $header->name . '</th>';
            }
            $table .= '</tr>';

            // Print table rows.
            foreach ($results->getRows() as $row) {
                $table .= '<tr>';
                foreach ($row as $cell) {
                    $table .= '<td>'
                        . htmlspecialchars($cell, ENT_NOQUOTES)
                        . '</td>';
                }
                $table .= '</tr>';
            }
            $table .= '</table>';

        } else {
            $table .= '<p>No Results Found.</p>';
        }
        return $table;
    }
}