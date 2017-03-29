<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function loginAction(Request $request)
    {
//        $form = $this->createForm(SignIn::class);
//
//        $authenticationUtils = $this->get('security.authentication_utils');
//        $error = $authenticationUtils->getLastAuthenticationError();
//        $lastUsername = $authenticationUtils->getLastUsername();
//
//        return $this->render('@App/HostAccount/signIn.html.twig', [
//            'lastUsername' => $lastUsername,
//            'error' => $error,
//            'signInForm' => $form->createView(),
//        ]);
    }

    public function logoutAction(Request $request)
    {
    }

    public function indexAction(Request $request)
    {
    }
}
