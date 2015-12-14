<?php
namespace AppBundle\Service;

use AppBundle\Report\ProductReport;

class DifficultCases extends ProductReport
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
        $query = $this->entityManager->createQueryBuilder();
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->andWhere('ps.uniqueViews > 0')
            ->setParameter('timeKey', $this->timeKey)
            ->addOrderBy('ps.uniqueViews', 'ASC')
            ->addOrderBy('ps.conversion', 'ASC')
            ->setMaxResults($this->limit);
        $list = $query->getQuery()->getResult();
        if (!$list) {
            return;
        }
        $ids = array();
        foreach ($list as $item) {
            $ids[] = $item->getProductId();
        }

        $query = $this->entityManager->createQueryBuilder();
        $where = $query->expr()->in('p.productId', $ids);
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->add('where', $where)
            ->andWhere('ps.timeKey = :timeKey')
            ->setParameter('timeKey', $this->timeKey)
            ->addOrderBy('ps.conversion', 'ASC')
            ->addOrderBy('ps.uniqueViews', 'ASC')
            ->addOrderBy('p.availableDate', 'ASC');
        $this->list = $query->getQuery()->getResult();
    }
}