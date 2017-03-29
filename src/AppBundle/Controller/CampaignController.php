<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use AppBundle\Forms\CampaignRegistrationType;
use AppBundle\Dtos\CampaignRegistration;
use AppBundle\Models\Campaign;
use AppBundle\Models\UtcDate;

class CampaignController extends Controller
{
    public function listAction(Request $request)
    {
    }

    public function registrationAction(Request $request)
    {
    	$form = $this->createForm(CampaignRegistrationType::class, new CampaignRegistration());

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $campaignRegistration = $form->getData();

                $userId = 1;

                $campaign = $this->get('app.campaign.registerer')->create($campaignRegistration, $userId);

                $em = $this->getDoctrine()->getManager();
                $em->persist($campaign);
                $em->flush();

                return $this->redirect("/");
            }
        }

        return $this->render(
            'AppBundle:Campaign:campaignRegistration.html.twig', [
                'campaignRegistrationForm' => $form->createView()
            ]
        );
    }
}
