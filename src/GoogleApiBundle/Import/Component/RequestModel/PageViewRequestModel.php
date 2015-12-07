<?php

namespace GoogleApiBundle\Import\Component\RequestModel;

use CronBundle\Import\Component\RequestModel;


class PageViewRequestModel extends RequestModel
{
    /** @var array */
    protected $dimensions = array(
        'ga:pagePath',
    );

    /** @var array */
    protected $metrics = array(
        'ga:pageviews',
        'ga:uniquePageviews',
    );

    /** @var array */
    protected $sorts = array(
        '-ga:uniquePageviews',
        '-ga:pageviews',
    );

    /** @var array */
    protected $filters = array(
        'ga:pagePath' => array(
            '!@index.php',
            '!@.html',
            '!@page=',
            '!@?',
            '!~\/([a-zA-Z0-9-_])*\/([a-zA-Z0-9-_])*',
        ),
    );

    /** @var string */
    protected $startDate = '';

    /** @var string */
    protected $finishDate = '';

    public function setActualMonthlyDateInterval()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P30D'));
        $this->startDate = $date->format('Y-m-d');
    }

    public function setLastMonthlyDateInterval()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P60D'));
        $this->startDate = $date->format('Y-m-d');
        $date = new \DateTime();
        $date->sub(new \DateInterval('P31D'));
        $this->finishDate = $date->format('Y-m-d');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCollectionRequest()
    {
        $this->collection = array(
            'dimensions' => $this->dimensions,
            'metrics' => $this->metrics,
            'sorts' => $this->sorts,
            'filters' => $this->filters,
        );
        if ($this->startDate) {
            $this->collection['startDate'] = $this->startDate;
        }
        if ($this->finishDate) {
            $this->collection['finishDate'] = $this->finishDate;
        }
        return parent::getCollectionRequest();
    }

    /**
     * @param string $key
     * @return string
     * @throws \Exception
     */
    public function getItemRequest($key = '')
    {
        $this->item = '';
        return parent::getItemRequest($key);
    }
}