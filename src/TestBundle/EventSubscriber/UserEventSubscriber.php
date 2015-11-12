<?php
namespace TestBundle\EventSubscriber;

use TestBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class UserEventSubscriber implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof User) {
            /** @var User $user */
            $user = $args->getEntity();
            $createDate = $user->getCreateDate();
            if (!$createDate) {
                $user->setCreateDate(new \DateTime());
            }
        }
    }
}