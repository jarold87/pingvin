<?php

namespace FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestPageController extends Controller
{
    /**
     * @Route("/TestPage", name="testpage")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $testService = $this->get('test_service');
        return $this->render('FrontBundle::home.html.twig', array(
            'test' => $testService->testAction(),
        ));
    }
}
