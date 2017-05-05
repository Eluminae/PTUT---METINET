<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Administration\UserManagerInterface;

class JurorController extends Controller implements UserManagerInterface
{
    public function indexAction()
    {

    }

    public function loginAction(Request $request)
    {
        // TODO: Implement loginAction() method.
    }

    public function logoutAction(Request $request)
    {
        // TODO: Implement logoutAction() method.
    }
}
