<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use AppBundle\Forms\RealisationRegistrationType;
use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Models\Campaign;
use AppBundle\Models\UtcDate;

class RealisationController extends Controller
{
    public function listForCampaignAction(Request $request, string $campaignId)
    {
        $campaign = $this->get('app.campaign.repository')->findOneById($campaignId);
        if (null === $campaign) {
            throw new \Exception(sprintf('Campaign %s not found.', $campaignId));
        }

        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaignId);
        return $this->render(
            'AppBundle:Default:Realisation/listForCampaign.html.twig', [

                'realisations' => $realisations,
            ]
        );
    }

    public function registerAction(Request $request, string $campaignId)
    {
        $campaign = $this->get('app.campaign.repository')->findOneById($campaignId);
        if (null === $campaign) {
            throw new \Exception(sprintf('Campaign %s not found.', $campaignId));
        }

        $form = $this->createForm(RealisationRegistrationType::class, new RealisationRegistration());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $realisationRegistration = $form->getData();

            $realisation = $this->get('app.realisation.registerer')->create($realisationRegistration, $campaignId);

            $em = $this->getDoctrine()->getManager();
            $em->persist($realisation);
            $em->flush();

            return $this->redirect("/");
        }

        return $this->render(
            'AppBundle:Default:Realisation/create.html.twig', [
                'realisationRegistrationForm' => $form->createView(),
                'campaign' => $campaign,
            ]
        );
    }
}
