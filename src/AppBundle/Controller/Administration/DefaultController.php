<?php

namespace AppBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @param Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $campaigns = $this->get('app.campaign.repository')->findBy([], [], 4);

        return $this->render(
            'AppBundle:Admin:index.html.twig',
            [
                'campaigns' => $campaigns,
            ]
        );
    }
}
