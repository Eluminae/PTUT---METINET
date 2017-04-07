<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Config\Definition\Exception\Exception;

use AppBundle\Dtos\CampaignCreation;
use AppBundle\Forms\CampaignCreationType;
use AppBundle\Models\Campaign;
use AppBundle\Models\UtcDate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CampaignController extends Controller
{
    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Campaign $campaign)
    {
        return $this->render(
            'AppBundle:Default:Campaign/show.html.twig', [
                'campaign' => $campaign
            ]
        );
    }

    public function listAction(Request $request)
    {
        $campaigns = $this->get('app.campaign.repository')->findAll();

        return $this->render(
            'AppBundle:Default:Campaign/list.html.twig', [
                'campaigns' => $campaigns
            ]
        );
    }
}
