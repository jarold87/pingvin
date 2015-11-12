<?php
namespace TestBundle\Repository;

use Doctrine\ORM\EntityRepository;

class User extends EntityRepository
{
    public function findAllOrderedByName()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT u FROM TestBundle:User u ORDER BY u.username ASC'
            )
            ->getResult();
    }
}