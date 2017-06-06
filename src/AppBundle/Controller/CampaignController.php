<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Models\Campaign;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CampaignController extends Controller
{
    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     */
    public function showAction(Request $request, Campaign $campaign)
    {
        if (false === $campaign->isActive()) {
            throw $this->createNotFoundException('Cette campagne n\'existe pas.');
        }

        return $this->render(
            'AppBundle:Default:Campaign/show.html.twig',
            [
                'campaign' => $campaign,
            ]
        );
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showResultAction(Request $request, Campaign $campaign)
    {
        if ($campaign->isActive()) {
            throw $this->createNotFoundException('Cette campagne n\'est pas terminée.');
        }
        if (false === $campaign->isResultsPublished()) {
            throw $this->createNotFoundException('Cette campagne n\'existe pas.');
        }

        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign);

        return $this->render(
            'AppBundle:Default:Campaign/showResult.html.twig', [
                'campaign' => $campaign,
                'realisations' => $realisations,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFinishedAction(Request $request)
    {
        $campaigns = $this->get('app.campaign.repository')->findFinished();

        return $this->render(
            'AppBundle:Default:Campaign/listFinished.html.twig', [
                'campaigns' => $campaigns,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $campaignsApprovedActive = $this->get('app.campaign.repository')->findApprovedActive();

        return $this->render(
            'AppBundle:Default:Campaign/list.html.twig',
            [
                'campaigns' => $campaignsApprovedActive,
            ]
        );
    }
}
