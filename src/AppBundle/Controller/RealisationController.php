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
    public function listAction(Request $request)
    {
    }

    public function registerAction(Request $request, string $campaignId)
    {
        $form = $this->createForm(RealisationRegistrationType::class, new RealisationRegistration());

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $realisationRegistration = $form->getData();

                $realisation = $this->get('app.realisation.registerer')->create($realisationRegistration, $campaignId);

                $em = $this->getDoctrine()->getManager();
                $em->persist($realisation);
                $em->flush();

                return $this->redirect("/");
            }
        }

        $campaign = $this->get('app.campaign.repository')->findOneById($campaignId);
        return $this->render(
            'AppBundle:Realisation:realisationRegistration.html.twig', [
                'realisationRegistrationForm' => $form->createView(),
                'campaign' => $campaign
            ]
        );
    }
}
