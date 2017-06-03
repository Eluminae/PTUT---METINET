<?php

namespace AppBundle\Controller\Administration;

use AppBundle\Models\Campaign;
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
        $campaignsApproved = $this->get('app.campaign.repository')->findBy(
            [
                'status' => Campaign::ACCEPTED
            ],
            [],
            4
        );

        $user = $this->get('security.token_storage')->getToken()->getUser();
        foreach ($campaignsApproved as $key => $campaignApproved) {
            if (
                false === $this->get('app.user.authorization_checker')->isAllowedToShowCampaign($user, $campaignApproved)
            ) {
                unset($campaignsApproved[$key]);
            }
        }

        return $this->render(
            'AppBundle:Admin:index.html.twig',
            [
                'campaigns' => $campaignsApproved,
            ]
        );
    }
}
