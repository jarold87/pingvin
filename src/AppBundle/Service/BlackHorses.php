<?php
namespace AppBundle\Service;

use AppBundle\Report\ProductReport;

class BlackHorses extends ProductReport
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
            ->andWhere('ps.uniqueViews > :minimumUniqueViews')
            ->andWhere('ps.conversion > :minimumConversion')
            ->setParameter('timeKey', $this->timeKey)
            ->setParameter('minimumUniqueViews', $this->minimumUniqueViews)
            ->setParameter('minimumConversion', $this->minimumConversion)
            ->addOrderBy('ps.uniqueViews', 'DESC')
            ->addOrderBy('ps.conversion', 'ASC')
            ->setMaxResults($this->limit);
        $this->list = $query->getQuery()->getResult();
    }

    protected function loadStatistics()
    {
        foreach ($this->list as $product) {
            $statisticsEntities = $this->getProductStatistics($product);
            $this->loadOneProductStatistics($statisticsEntities, $product);
        }
    }

    protected function setRowsToReport()
    {
        if (!$this->collectedData) {
            return;
        }
        arsort($this->keysByView);
        asort($this->keysByConversion);
        foreach ($this->keysByView as $key => $view) {
            if (!$this->collectedData[$key]['conversion']) {
                $this->rowsToReport[] = $this->collectedData[$key];
            }
        }
        foreach ($this->keysByConversion as $key => $conversion) {
            if ($conversion) {
                $this->rowsToReport[] = $this->collectedData[$key];
            }
        }
    }
}