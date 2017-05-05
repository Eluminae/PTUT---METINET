<?php

namespace AppBundle\Controller;

use AppBundle\Forms\SignInType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function loginAction(Request $request)
    {
        $form = $this->createForm(SignInType::class);

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@App/Admin/signIn.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    public function logoutAction(Request $request)
    {
    }

    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }
}
