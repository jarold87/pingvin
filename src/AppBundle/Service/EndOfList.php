<?php
namespace AppBundle\Service;

use AppBundle\Report\ProductReport;

class EndOfList extends ProductReport
{
    /**
     * @return array
     */
    public function getReport()
    {
        if ($this->rowsToReport) {
            return $this->rowsToReport;
        }
        $this->loadList();
        $this->loadStatistics();
        $this->setRowsToReport();
        return $this->rowsToReport;
    }

    protected function loadList()
    {
        $maximumUniqueViews = $this->avgUniqueViews;
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
            ->andWhere('ps.uniqueViews < :maximumUniqueViews')
            ->andWhere('ps.conversion < :maximumConversion')
            ->setParameter('timeKey', $this->timeKey)
            ->setParameter('maximumUniqueViews', $maximumUniqueViews)
            ->setParameter('maximumConversion', $maximumConversion)
            ->addOrderBy('ps.conversion', 'ASC')
            ->addOrderBy('ps.uniqueViews', 'ASC')
            ->addOrderBy('ps.views', 'ASC')
            ->addOrderBy('p.availableDate', 'ASC')
            ->setMaxResults($this->limit);
        $this->list = $query->getQuery()->getResult();
    }
}