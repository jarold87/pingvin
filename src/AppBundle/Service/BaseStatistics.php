<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class BaseStatistics
{
    /** @var array */
    protected $existKey = array(
        'AvgUniqueViews',
        'AvgConversion',
        'AvgUniqueViewsAtInteractiveProducts',
        'AvgConversionAtInteractiveProducts',
        'CheatCount',
        'NullStatisticsCount',
        'ProductCount',
    );

    /** @var int */
    protected $userId;

    /** @var string */
    protected $timeKey;

    /** @var EntityManager */
    protected $entityManager;

    /** @var array */
    protected $data = array();

    /**
     * @param $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param $key
     */
    public function setTimeKey($key)
    {
        $this->timeKey = $key;
    }

    /**
     * @param EntityManager $manager
     */
    public function setEntityManager(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    /**
     * @param $key
     * @param null $timeKey
     * @return mixed
     * @throws \Exception
     */
    public function get($key, $timeKey = null)
    {
        if (!in_array($key, $this->existKey)) {
            throw new \Exception('Missing Base Statistics Key!');
        }
        if (!$timeKey) {
            $timeKey = $this->timeKey;
        }
        if (isset($this->data[$this->userId][$timeKey][$key])) {
            return $this->data[$this->userId][$timeKey][$key];
        }
        $methodName = 'get' . $key;
        $value = $this->$methodName($timeKey);
        $this->setValue($key, $value, $timeKey);
        return $value;
    }

    protected function getAvgUniqueViews($timeKey)
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('avg(ps.calculatedUniqueViews)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', $timeKey)
            ->getQuery();
        $avgScore = $query->getQuery()->getResult();
        return round($avgScore[0][1], 2);
    }

    protected function getAvgConversion($timeKey)
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('avg(ps.calculatedConversion)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', $timeKey)
            ->getQuery();
        $avgScore = $query->getQuery()->getResult();
        return round($avgScore[0][1], 2);
    }

    protected function getAvgUniqueViewsAtInteractiveProducts($timeKey)
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('avg(ps.calculatedUniqueViews)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('ps.calculatedViews > 0')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', $timeKey)
            ->getQuery();
        $avgScore = $query->getQuery()->getResult();
        return round($avgScore[0][1], 2);
    }

    protected function getAvgConversionAtInteractiveProducts($timeKey)
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('avg(ps.calculatedConversion)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('ps.calculatedViews > 0')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', $timeKey)
            ->getQuery();
        $avgScore = $query->getQuery()->getResult();
        return round($avgScore[0][1], 2);
    }

    protected function getCheatCount($timeKey)
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('count(ps.productStatisticsId)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('ps.isCheat = 1')
            ->setParameter('timeKey', $timeKey)
            ->getQuery();
        $countScore = $query->getQuery()->getResult();
        return round($countScore[0][1]);
    }

    protected function getNullStatisticsCount($timeKey)
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('count(ps.productStatisticsId)')
            ->from('AppBundle:ProductStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('ps.calculatedViews = 0')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', 'all')
            ->getQuery();
        $countScore = $query->getQuery()->getResult();
        return round($countScore[0][1]);
    }

    protected function getProductCount($timeKey)
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('count(p.productId)')
            ->from('AppBundle:Product', 'p')
            ->where('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->getQuery();
        $countScore = $query->getQuery()->getResult();
        return round($countScore[0][1]);
    }

    /**
     * @param $key
     * @param $value
     * @param $timeKey
     */
    protected function setValue($key, $value, $timeKey)
    {
        $this->data[$this->userId][$timeKey][$key] = $value;
    }

    protected function reset()
    {
        $this->data = array();
    }
}