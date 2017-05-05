<?php

namespace AppBundle\Controller\Administration;

use AppBundle\Forms\SignInType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Administration\UserManagerInterface;

class AdminController extends Controller
{
    public function indexAction(Request $request)
    {
		return $this->render('AppBundle:Admin:index.html.twig');
    }
}
