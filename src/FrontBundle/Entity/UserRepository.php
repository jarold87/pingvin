<?php
namespace FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
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