<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;


class Setting
{
    /** @var array */
    protected $settings = array();

    /** @var EntityManager */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $settings = $shop_type = $entityManager->getRepository('AppBundle:Setting')->findAll();
        if ($settings) {
            foreach ($settings as $setting) {
                $this->settings[$setting->getName()] = $setting->getValue();
            }
        }
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        if (isset($this->settings[$name])) {
            return $this->settings[$name];
        }
        return null;
    }
}