<?php
namespace AppBundle\Service;

use AppBundle\Report\ProductReport;

class StarProducts extends ProductReport
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
        $query = $this->entityManager->createQueryBuilder();
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->andWhere('ps.isCheat = 0')
            ->setParameter('timeKey', $this->timeKey)
            ->addOrderBy('ps.calculatedUniqueViews', 'DESC')
            ->addOrderBy('ps.calculatedViews', 'DESC')
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
            ->addOrderBy('ps.calculatedUniqueViews', 'DESC')
            ->addOrderBy('ps.calculatedViews', 'DESC');
        $this->list = $query->getQuery()->getResult();
    }

    protected function loadListB()
    {
        $minimumUniqueViews = $this->avgUniqueViews * 3;
        $minimumConversion = 0;
        if ($this->avgConversion > 0) {
            $minimumConversion = $this->avgConversion * 3;
        }

        $query = $this->entityManager->createQueryBuilder();
        $query->select(array('p', 'ps'))
            ->from('AppBundle:Product', 'p')
            ->leftJoin('p.productStatistics', 'ps')
            ->where('ps.timeKey = :timeKey')
            ->andWhere('p.status = 1')
            ->andWhere('p.isDead = 0')
            ->andWhere('ps.isCheat = 0')
            ->andWhere('ps.calculatedUniqueViews > :minimumUniqueViews')
            ->andWhere('ps.calculatedConversion > :minimumConversion')
            ->setParameter('timeKey', $this->timeKey)
            ->setParameter('minimumUniqueViews', $minimumUniqueViews)
            ->setParameter('minimumConversion', $minimumConversion)
            ->addOrderBy('ps.calculatedScore', 'DESC')
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
            ->setParameter('timeKey', $this->timeKey)
            ->addOrderBy('ps.calculatedScore', 'DESC')
            ->addOrderBy('ps.calculatedConversion', 'DESC')
            ->addOrderBy('ps.calculatedUniqueViews', 'DESC');
        $this->list = $query->getQuery()->getResult();
    }
}