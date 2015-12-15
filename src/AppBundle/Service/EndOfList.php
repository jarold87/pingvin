<?php
namespace AppBundle\Service;

use AppBundle\Report\ProductReport;

class EndOfList extends ProductReport
{
    /**
     * @param $version
     * @return array
     */
    public function getReport($version)
    {
        if ($this->rowsToReport) {
            return $this->rowsToReport;
        }
        $this->calculateLimit();
        $this->loadList($version);
        $this->loadStatistics();
        $this->setRowsToReport();
        return $this->rowsToReport;
    }

    protected function loadList($version)
    {
        if ($version == 'A') {
            $this->loadListA();
        } else {
            $this->loadListB();
        }
    }

    protected function loadListA()
    {
        $maximumUniqueViews = $this->avgUniqueViews * 3;
        $maximumConversion = 100;
        if ($this->avgConversion > 0) {
            $maximumConversion = $this->avgConversion;
        }
        $query = $this->entityManager->createQueryBuilder();
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->andWhere('ps.calculatedConversion = 0')
            ->setParameter('timeKey', $this->timeKey)
            ->addOrderBy('ps.calculatedUniqueViews', 'ASC')
            ->addOrderBy('ps.calculatedViews', 'ASC')
            ->addOrderBy('p.availableDate', 'ASC')
            ->setMaxResults($this->limit);
        $this->list = $query->getQuery()->getResult();
    }

    protected function loadListB()
    {
        $maximumUniqueViews = $this->avgUniqueViews * 3;
        $maximumConversion = 100;
        if ($this->avgConversion > 0) {
            $maximumConversion = $this->avgConversion;
        }
        $query = $this->entityManager->createQueryBuilder();
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->andWhere('ps.calculatedUniqueViews > 0')
            ->andWhere('ps.calculatedUniqueViews <= :maximumUniqueViews')
            ->andWhere('ps.calculatedConversion <= :maximumConversion')
            ->setParameter('timeKey', $this->timeKey)
            ->setParameter('maximumUniqueViews', $maximumUniqueViews)
            ->setParameter('maximumConversion', $maximumConversion)
            ->addOrderBy('ps.calculatedConversion', 'ASC')
            ->addOrderBy('ps.calculatedUniqueViews', 'DESC')
            ->addOrderBy('ps.calculatedViews', 'DESC')
            ->addOrderBy('p.availableDate', 'ASC')
            ->setMaxResults($this->limit);
        $this->list = $query->getQuery()->getResult();
    }
}