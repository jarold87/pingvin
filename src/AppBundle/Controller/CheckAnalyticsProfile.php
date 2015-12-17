<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GoogleApiBundle\Service\AnalyticsService;

class CheckAnalyticsProfile extends Controller
{
    /** @var AnalyticsService */
    protected $analyticsService;

    /**
     * @Route("/check_analytics", name="Check Analytics Profile")
     */
    public function indexAction(Request $request)
    {
        if ($request->query->get('profile_id')) {
            $profileId = $request->query->get('profile_id');
            $this->analyticsService = $this->get('AnalyticsService');
            $this->analyticsService->setProfileId($profileId);
            $this->analyticsService->checkProfile();
        }

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array());
    }
}
