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
