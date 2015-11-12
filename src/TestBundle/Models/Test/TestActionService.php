<?php
namespace TestBundle\Models\Test;

use TestBundle\Event\TestEvent;
use TestBundle\TestEvents;
use TestBundle\Models\EventDispatcher\TestEventDispatcherService;

class TestActionService
{
    public function testAction(TestEventDispatcherService $dispatcher)
    {
        $data = array();
        $testEvent = new TestEvent($data);
        $dispatcher->dispatch(TestEvents::SAVE_ACTION, $testEvent);
    }
}