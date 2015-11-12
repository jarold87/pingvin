<?php
namespace TestBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class TestEvent extends Event
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function addRootDirToData($rootDir)
    {
        $this->data['rootDir'] = $rootDir;
    }

    public function getData($name)
    {
        return $this->data[$name];
    }
}