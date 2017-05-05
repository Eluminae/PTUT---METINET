<?php

namespace AppBundle\Controller\Administration;

use AppBundle\Forms\SignInType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\UserManagerInterface;

class AdminController extends Controller implements UserManagerInterface
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

    public function indexAction(Request $request)
    {
        $campaignAdministrators = $this->get('app.campaign_administrator.repository')->findAll();
        $administrators = $this->get('app.administrator.repository')->findAll();
        $jurors = $this->get('app.juror.repository')->findAll();

		return $this->render('AppBundle:Admin:index.html.twig', [
            'campaignAdministrators' => $campaignAdministrators,
            'administrators' => $administrators,
            'jurors' => $jurors
        ]);
    }
}
