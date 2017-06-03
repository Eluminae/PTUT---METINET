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
        $numberOfResults = 4;

        $campaignsApproved = $this->get('app.campaign.repository')->findByStatus(Campaign::ACCEPTED);

        $user = $this->get('security.token_storage')->getToken()->getUser();
        foreach ($campaignsApproved as $key => $campaignApproved) {
            if (
                false === $this->get('app.user.authorization_checker')->isAllowedToShowCampaign($user, $campaignApproved)
            ) {
                unset($campaignsApproved[$key]);
            }
        }
        $campaignsApproved = array_splice($campaignsApproved, 0, $numberOfResults);

        $realisations = [];
        foreach ($campaignsApproved as $campaign) {
            $realisations += $this->get('app.realisation.repository')->findByCampaign($campaign);
        }
        $realisations = array_splice($realisations, 0, $numberOfResults);

        $users = [];
        if ($this->get('app.user.authorization_checker')->isAllowedToListUsers($user)) {
            $campaignAdministrators = $this->get('app.campaign_administrator.repository')->findAll();
            $jurors = $this->get('app.juror.repository')->findAll();
            $administrators = $this->get('app.administrator.repository')->findAll();

            $users = array_merge($campaignAdministrators, $jurors);
            $users = array_merge($users, $administrators);

            $users = array_splice($users, 0, $numberOfResults);
        }

        return $this->render(
            'AppBundle:Admin:index.html.twig',
            [
                'campaigns' => $campaignsApproved,
                'realisations' => $realisations,
                'users' => $users,
            ]
        );
    }
}
