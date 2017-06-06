<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Forms\RealisationRegistrationType;
use AppBundle\Models\Campaign;
use AppBundle\Models\Realisation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class RealisationController extends Controller
{
    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listForCampaignAction(Request $request, Campaign $campaign)
    {
        if (false === $realisation->getCampaign()->isResultsPublished()) {
            throw $this->createNotFoundException('Cette campagne n\'existe pas.');
        }

        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign->getId());

        return $this->render(
            'AppBundle:Default:Realisation/listForCampaign.html.twig',
            [
                'realisations' => $realisations,
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
     *
     * @throws \LogicException
     */
    public function registerAction(Request $request, Campaign $campaign)
    {
        $form = $this->createForm(RealisationRegistrationType::class, new RealisationRegistration());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $realisationRegistration = $form->getData();

            $realisation = $this->get('app.realisation.registerer')->create(
                $realisationRegistration,
                $campaign->getId()
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($realisation);
            $em->flush();

            $this->addFlash('success', 'Votre realisation a bien été prise en compte');

            return $this->redirectToRoute('public.homepage');
        }

        return $this->render(
            'AppBundle:Default:Realisation/registration.html.twig',
            [
                'realisationRegistrationForm' => $form->createView(),
                'campaign' => $campaign,
            ]
        );
    }

    /**
     * @param Request     $request
     * @param Realisation $realisation
     *
     * @ParamConverter("realisation", class="AppBundle:Realisation")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Realisation $realisation)
    {
        if (false === $realisation->getCampaign()->isResultsPublished()) {
            throw $this->createNotFoundException('Cette réalisation n\'existe pas.');
        }

        return $this->render(
            'AppBundle:Default:Realisation/show.html.twig',
            [
                'realisation' => $realisation,
            ]
        );
    }

    /**
     * @param Request     $request
     * @param Realisation $realisation
     *
     * @ParamConverter("realisation", class="AppBundle:Realisation")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadAction(Request $request, Realisation $realisation)
    {
        if (false === $realisation->getCampaign()->isResultsPublished()) {
            throw $this->createNotFoundException('Cette réalisation n\'existe pas.');
        }

        return $this->file($realisation->getFilePath());
    }
}
