<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Config\Definition\Exception\Exception;

use AppBundle\Forms\CampaignCreationType;
use AppBundle\Dtos\CampaignCreation;
use AppBundle\Models\Campaign;
use AppBundle\Models\UtcDate;

class CampaignController extends Controller
{
    public function showAction(Request $request)
    {
        $campaignId = $request->get('campaignId');

        $campaign = $this->get('app.campaign.repository')->findOneById($campaignId);

        if ($campaign === null) {
            throw new Exception("Pas de campage avec cet id");
        }

        return $this->render(
            'AppBundle:Default:Campaign/showCampaign.html.twig', [
                'campaign' => $campaign
            ]
        );
    }

    public function listAction(Request $request)
    {
        $campaignsApproved = $this->get('app.campaign.repository')->findByReview(true);

        return $this->render(
            'AppBundle:Default:Campaign/listCampaign.html.twig', [
                'campaigns' => $campaignsApproved
            ]
        );
    }
}
