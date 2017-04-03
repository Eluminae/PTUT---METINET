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
            'AppBundle:Campaign:showCampaign.html.twig', [
                'campaign' => $campaign
            ]
        );
	}

    public function listAction(Request $request)
    {
        $campaigns = $this->get('app.campaign.repository')->findAll();

        return $this->render(
            'AppBundle:Campaign:listCampaign.html.twig', [
                'campaigns' => $campaigns
            ]
        );
    }

    public function createAction(Request $request)
    {
    	$form = $this->createForm(CampaignCreationType::class, new CampaignCreation());

        
    	$form->handleRequest($request);
    	if ($form->isSubmitted() && $form->isValid()) {
    		$campaignCreation = $form->getData();

    		$userId = $this->getUser()->getIdentity()->getId();

    		$campaign = $this->get('app.campaign.creator')->create($campaignCreation, $userId);

    		$em = $this->getDoctrine()->getManager();
    		$em->persist($campaign);
    		$em->flush();

    		return $this->redirect("/");
    	}
        

        return $this->render(
            'AppBundle:Campaign:campaignCreation.html.twig', [
                'campaignCreationForm' => $form->createView()
            ]
        );
    }
}
