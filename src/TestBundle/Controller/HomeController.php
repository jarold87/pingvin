<?php

namespace TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TestBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TestBundle\EventListener\TestEventListener;
use TestBundle\EventSubscriber\TestEventSubscriber;

class HomeController extends Controller
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    /** @var \TestBundle\Models\EventDispatcher\TestEventDispatcherService */
    protected $dispatcherObject;

    /**
     * @Route("/", name="front_homepage")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->em = $this->getDoctrine()->getManager('default');

        $user = $this->searchUser();
        if (!$user) {
            $this->newUser();
        } else {
            $this->editUser($user);
        }

        $this->eventTest();

        return $this->testService();
    }

    protected function eventTest()
    {
        $this->dispatcherObject = $this->get('test_event_dispatcher');
        $this->dispatcherObject->setRootDir($this->get('kernel')->getRootDir());

        $this->addListener();
        $this->addSubscriber();

        // A TestActionService fogja kiváltani az eseményt DIC segítségével.
        $this->testActionService();
    }

    protected function addListener()
    {
        $listener = new TestEventListener();
        $this->dispatcherObject->addListener('front.save_action', array($listener, 'createExport'));
    }

    protected function addSubscriber()
    {
        $subscriber = new TestEventSubscriber();
        $this->dispatcherObject->addSubscriber($subscriber);
    }

    /**
     * @return \TestBundle\Entity\User
     */
    protected function searchUser()
    {
        $user = $this->em->getRepository('TestBundle:User')->findOneBy(
            array('username' => 'Test')
        );
        return $user;
    }

    protected function newUser()
    {
        $user = new User();
        $user->setUsername('Test');
        $user->setPassword(md5('test' . rand(0,9)));
        $user->setCreateDate(new \DateTime('2014-01-01'));

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param $user \TestBundle\Entity\User
     */
    protected function editUser($user)
    {
        $user->setPassword(md5('test' . rand(0,9)));

        $this->em->flush();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function testService()
    {
        $testService = $this->get('test_service');
        return $this->render('TestBundle::home.html.twig', array(
            'test' => $testService->testAction(),
        ));
    }

    protected function testActionService()
    {
        $testService = $this->get('test_action_service');
        $testService->testAction($this->get('test_event_dispatcher'));
    }
}
