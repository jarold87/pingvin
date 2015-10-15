<?php
namespace FrontBundle\Repository;

use Doctrine\ORM\EntityRepository;

class User extends EntityRepository
{
    public function findAllOrderedByName()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT u FROM FrontBundle:User u ORDER BY u.username ASC'
            )
            ->getResult();
    }
}