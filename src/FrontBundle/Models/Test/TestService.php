<?php

namespace FrontBundle\Models\Test;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestService extends Controller
{
    public function testAction()
    {
        return 'TEXT FROM TEST SERVICE';
    }
}