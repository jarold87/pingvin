<?php

namespace FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FrontBundle\Entity\User;

class HomeController extends Controller
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    /**
     * @Route("/", name="front_homepage")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->em = $this->getDoctrine()->getManager();

        $user = $this->searchUser();
        if (!$user) {
            $this->newUser();
        } else {
            $this->editUser($user);
        }
        return $this->testService();
    }

    /**
     * @return \FrontBundle\Entity\User
     */
    protected function searchUser()
    {
        $user = $this->em->getRepository('FrontBundle:User')->findOneBy(
            array('username' => 'Test')
        );
        return $user;
    }

    protected function newUser()
    {
        $user = new User();
        $user->setUsername('Test');
        $user->setPassword(md5('test' . rand(0,9)));
        $user->setCreateDate(new \DateTime("now"));
        $user->setUpdateDate(new \DateTime("now"));

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param $user \FrontBundle\Entity\User
     */
    protected function editUser($user)
    {
        $user->setPassword(md5('test' . rand(0,9)));
        $user->setUpdateDate(new \DateTime("now"));

        $this->em->flush();
    }

    protected function testService()
    {
        $testService = $this->get('test_service');
        return $this->render('FrontBundle::home.html.twig', array(
            'test' => $testService->testAction(),
        ));
    }
}
