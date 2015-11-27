<?php

namespace TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GoogleApiBundle\Services\Analytics;
use \Google_Client as GoogleClient;


class gaTest2 extends Controller
{
    /** @var GoogleClient */
    protected $client;

    /** @var Analytics */
    protected $analyticsService;

    /**
     * @Route("/gatest2", name="front_ga2")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->client = $this->get('GoogleClient');
        $this->client->setAuthConfig($this->getParameter('googleCredentialsFile'));
        $this->client->setApplicationName('Pingvin');
        $this->client->setScopes($this->getParameter('googleAnalyticsAuthUrl'));
        $this->analyticsService = $this->get('GoogleAnalyticsService');

        $accounts = $this->analyticsService->management_accounts->listManagementAccounts();
        $data = array();
        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();
            foreach ($items as $item) {
                $id = $item->getId();
                $properties = $this->analyticsService->management_webproperties
                    ->listManagementWebproperties($id);
                $items_e = $properties->getItems();
                foreach ($items_e as $item_e) {
                    $profiles = $this->analyticsService->management_profiles
                        ->listManagementProfiles($id, $item_e->getId());
                    $items_ee = $profiles->getItems();
                    foreach ($items_ee as $item_ee) {
                        $data[] = array($item_ee->getId(), $item_e->getId());
                    }
                }
            }
            var_dump('<pre>', $data);
        }

        return $this->render('CronBundle::message.html.twig', array(
            'message' => '...',
        ));
    }
}