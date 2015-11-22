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
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->reset();
        $this->entityManager = $entityManager;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        if (!$this->settings) {
            $this->loadSettings();
        }
        if (isset($this->settings[$name])) {
            return $this->settings[$name];
        }
        return null;
    }

    protected function reset()
    {
        $this->settings = array();
    }

    protected function loadSettings()
    {
        $settings = $shop_type = $this->entityManager->getRepository('AppBundle:Setting')->findAll();
        if ($settings) {
            foreach ($settings as $setting) {
                $this->settings[$setting->getName()] = $setting->getValue();
            }
        }
    }
}