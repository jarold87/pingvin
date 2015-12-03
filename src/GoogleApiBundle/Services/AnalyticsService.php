<?php

namespace GoogleApiBundle\Services;

use \Google_Service_Analytics as GoogleServiceAnalytics;

class AnalyticsService
{
    /** @var int max 10000 */
    protected $maxResults = 10000;

    /** @var GoogleServiceAnalytics */
    protected $analyticsService;

    /** @var string */
    protected $profileId = '';

    /** @var string */
    protected $serviceId = '';

    /**
     * @param GoogleServiceAnalytics $service
     */
    public function setService(GoogleServiceAnalytics $service)
    {
        $this->analyticsService = $service;
    }

    /**
     * @param $id
     */
    public function setProfileId($id)
    {
        $this->profileId = $id;
    }

    /**
     * @param $request
     * @return string
     *
     * startDate (optional)
     * finishDate (optional)
     * dimensions (required)
     * metrics (required)
     * sorts (optional)
     * filters (optional)
     */
    public function getRequest(array $request)
    {
        if (!$this->serviceId) {
            $this->init();
        }
        $this->validateRequest($request);
        $now = new \DateTime();
        $yesterday = $now->sub(new \DateInterval('P1D'));
        $startDate = (isset($request['startDate'])) ? $request['startDate'] : '2005-01-01';
        $finishDate = (isset($request['finishDate'])) ? $request['finishDate'] : $yesterday->format('Y-m-d');
        $dimensions = $request['dimensions'];
        $metrics = $request['metrics'];
        $sorts = (isset($request['sorts'])) ? $request['sorts'] : null;
        $filters = (isset($request['filters'])) ? $request['filters'] : null;

        $optParams['dimensions'] = join(',', $dimensions);
        if ($sorts) {
            $optParams['sort'] = join(',', $sorts);
        }
        if ($filters) {
            $filterStrings = array();
            foreach ($filters as $field => $values) {
                if (is_array($values)) {
                    foreach ($values as $value) {
                        $filterStrings[] = $field . $value;
                    }
                } else {
                    $filterStrings[] = $field . $values;
                }
            }
            $optParams['filters'] = urlencode(join(';', $filterStrings));
        }
        $optParams['max-results'] = $this->maxResults;

        $results = $this->analyticsService->data_ga->get(
            'ga:' . $this->serviceId,
            $startDate,
            $finishDate,
            join(',', $metrics),
            $optParams,
            []
        );
        if (count($results->getRows()) > 0) {
            return $results;
        }
        return null;
    }

    public function test()
    {
        if (!$this->serviceId) {
            $this->init();
        }
        $metrics = array(
            'ga:entrances',
            'ga:pageviews',
            'ga:uniquePageviews',
            'ga:exitRate'
        );
        $optParams = array(
            'dimensions' => 'ga:pagePath,ga:pageTitle',
            'sort' => '-ga:uniquePageviews,-ga:pageviews',
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

        return $this->convertToDataTable($response);
    }

    protected function init()
    {
        if (!$this->profileId) {
            throw new \Exception('Missing profileId!');
        }
        $this->loadServiceId();
        if (!$this->serviceId) {
            throw new \Exception('Missing serviceId!');
        }
    }

    protected function loadServiceId()
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
                //var_dump('<pre>', $properties);
                if (count($properties->getItems()) > 0) {
                    $items = $properties->getItems();
                    $firstPropertyId = $items[0]->getId();
                    $profiles = $this->analyticsService->management_profiles
                        ->listManagementProfiles($accountId, $firstPropertyId);
                    //var_dump('<pre>', $profiles);
                    if (count($profiles->getItems()) > 0) {
                        $items = $profiles->getItems();
                        $this->serviceId = $items[0]->getId();
                    }
                }
            }
        }
    }

    /**
     * @param $request
     * @throws \Exception
     */
    protected function validateRequest($request)
    {
        if (!isset($request['dimensions'])) {
            throw new \Exception('Missing dimensions!');
        }
        if (!isset($request['metrics'])) {
            throw new \Exception('Missing metrics!');
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
