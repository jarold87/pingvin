<?php

namespace GoogleApiBundle\Service;

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

    /** @var string */
    protected $error = '';

    /** @var */
    protected $profileInfo;

    /**
     * @param GoogleServiceAnalytics $service
     */
    public function setService(GoogleServiceAnalytics $service)
    {
        $this->analyticsService = $service;
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function setProfileId($id)
    {
        $this->reset();
        if (!$id) {
            throw new \Exception('Missing profileId!');
        }
        $this->profileId = $id;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    public function checkProfile()
    {
        $this->loadServiceId();
        dump($this->profileInfo); die();
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
        if ($this->error) {
            return null;
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

        try {
            $results = $this->analyticsService->data_ga->get(
                'ga:' . $this->serviceId,
                $startDate,
                $finishDate,
                join(',', $metrics),
                $optParams,
                []
            );
        }
        catch (\Exception $e) {
            $this->error = 'Bad GA request: ' . $e->getMessage();
            return null;
        }
        if (count($results->getRows()) > 0) {
            return $results;
        }
        return null;
    }

    protected function init()
    {
        $this->loadServiceId();
    }

    protected function loadServiceId()
    {
        $id = $this->profileId;
        $accounts = $this->analyticsService->management_accounts->listManagementAccounts();
        if (!count($accounts->getItems())) {
            $this->error = 'Empty GA account list!';
            return;
        }
        $accountId = '';
        $items = $accounts->getItems();
        if (!count($items)) {
            $this->error = 'Missing GA account items!';
            return;
        }
        foreach ($items as $item) {
            if ($item->getId() == $id) {
                $accountId = $item->getId();
            }
        }
        if (!$accountId) {
            $this->error = 'Missing GA account ID!';
            return;
        }
        $properties = $this->analyticsService->management_webproperties
            ->listManagementWebproperties($accountId);
        if (!count($properties->getItems())) {
            $this->error = 'Missing GA account properties!';
            return;
        }
        $items = $properties->getItems();
        $this->profileInfo = $items;
        $firstPropertyId = $items[0]->getId();
        $profiles = $this->analyticsService->management_profiles
            ->listManagementProfiles($accountId, $firstPropertyId);
        if (!count($profiles->getItems())) {
            $this->error = 'Missing profiles!';
            return;
        }
        $items = $profiles->getItems();
        $this->serviceId = $items[0]->getId();
        if (!$this->serviceId) {
            $this->error = 'Missing service ID!';
            return;
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

    protected function reset()
    {
        $this->profileId = null;
        $this->serviceId = null;
    }
}
