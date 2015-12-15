<?php
namespace AppBundle\Service;

use AppBundle\Report\ProductReport;

class Potentials extends ProductReport
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

        $query = $this->entityManager->createQueryBuilder();
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->andWhere('ps.calculatedConversion > 0')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', $this->timeKey)
            ->addOrderBy('ps.calculatedUniqueViews', 'ASC')
            ->addOrderBy('ps.calculatedConversion', 'DESC')
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
            ->addOrderBy('ps.calculatedConversion', 'DESC')
            ->addOrderBy('ps.calculatedUniqueViews', 'ASC');
        $this->list = $query->getQuery()->getResult();
    }

    protected function loadListB()
    {
        $maximumUniqueViews = $this->avgUniqueViews * 3;

        $query = $this->entityManager->createQueryBuilder();
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->andWhere('ps.calculatedUniqueViews > 0')
            ->andWhere('ps.calculatedConversion > 0')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', $this->timeKey)
            ->addOrderBy('ps.calculatedConversion', 'DESC')
            ->addOrderBy('ps.calculatedUniqueViews', 'DESC')
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
            ->andWhere('ps.calculatedConversion > 0')
            ->andWhere('ps.calculatedUniqueViews <= :maximumUniqueViews')
            ->setParameter('timeKey', $this->timeKey)
            ->setParameter('maximumUniqueViews', $maximumUniqueViews)
            ->addOrderBy('ps.calculatedConversion', 'DESC')
            ->addOrderBy('ps.calculatedUniqueViews', 'DESC');
        $this->list = $query->getQuery()->getResult();
    }
}