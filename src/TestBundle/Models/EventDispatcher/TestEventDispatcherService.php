<?php
namespace TestBundle\Models\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use TestBundle\Event\TestEvent;
use TestBundle\Models\EventDispatcher\AbstractEventDispatcherService;


class TestEventDispatcherService extends AbstractEventDispatcherService
{
    /** @var string */
    protected $rootDir;

    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function dispatch($eventName, Event $event = null)
    {
        $event = $this->addRootDirToEventData($event);
        $this->dispatcher->dispatch($eventName, $event);
    }

    protected function addRootDirToEventData(TestEvent $event)
    {
        $event->addRootDirToData($this->rootDir);
        return $event;
    }
}