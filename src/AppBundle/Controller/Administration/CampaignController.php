<?php

namespace AppBundle\Controller\Administration;

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
    public function showAction(Request $request, string $campaignId)
	{
		$campaign = $this->get('app.campaign.repository')->findOneById($campaignId);

		if ($campaign === null) {
			throw new Exception("Pas de campage avec cet id");
		}

		return $this->render(
            'AppBundle:CampaignAdmin:show.html.twig', [
                'campaign' => $campaign
            ]
        );
	}

    public function listAction(Request $request)
    {
        $campaigns = $this->get('app.campaign.repository')->findAll();

        return $this->render(
            'AppBundle:CampaignAdmin:list.html.twig', [
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

            return $this->redirectToRoute('admin.campaign.list');
    	}
        
        return $this->render(
            'AppBundle:CampaignAdmin:create.html.twig', [
                'campaignCreationForm' => $form->createView()
            ]
        );
    }

    public function deleteAction(Request $request, string $campaignId)
    {
        $campaign = $this->get('app.campaign.repository')->findOneById($campaignId);
        if (null === $campaign) {
            throw new \Exception(sprintf('Campaign %s not found.', $campaignId));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($campaign);
        $em->flush();

        return $this->redirectToRoute('admin.campaign.list');
    }

    public function updateAction(Request $request)
    {
        // todo
    }
}
