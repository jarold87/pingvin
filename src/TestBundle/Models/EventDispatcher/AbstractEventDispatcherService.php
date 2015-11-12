<?php
namespace TestBundle\Models\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractEventDispatcherService
{
    /** @var EventDispatcher */
    protected $dispatcher;

    public function __construct()
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
    }

    public function dispatch($eventName, Event $event = null)
    {
        $this->dispatcher->dispatch($eventName, $event);
    }
}