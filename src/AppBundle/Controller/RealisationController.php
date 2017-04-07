<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use AppBundle\Dtos\RealisationMarkDto;
use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Forms\MarkType;
use AppBundle\Forms\RealisationRegistrationType;
use AppBundle\Models\Campaign;
use AppBundle\Models\Realisation;
use AppBundle\Models\UtcDate;
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
        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign->getId());

        return $this->render(
            'AppBundle:Default:Realisation/listForCampaign.html.twig', [
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
     */
    public function registerAction(Request $request, Campaign $campaign)
    {
        $form = $this->createForm(RealisationRegistrationType::class, new RealisationRegistration());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $realisationRegistration = $form->getData();

            $realisation = $this->get('app.realisation.registerer')->create($realisationRegistration, $campaign->getId());

            $em = $this->getDoctrine()->getManager();
            $em->persist($realisation);
            $em->flush();

            return $this->redirect("/");
        }

        return $this->render(
            'AppBundle:Default:Realisation/registration.html.twig', [
                'realisationRegistrationForm' => $form->createView(),
                'campaign' => $campaign
            ]
        );
    }

    /**
     * @param Request  $request
     * @param Realisation $realisation
     *
     * @ParamConverter("realisation", class="AppBundle:Realisation")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Realisation $realisation)
    {
        return $this->render(
            'AppBundle:Default:Realisation/show.html.twig', [
                'realisation' => $realisation,
            ]
        );
    }

    /**
     * @param Request  $request
     * @param Realisation $realisation
     *
     * @ParamConverter("realisation", class="AppBundle:Realisation")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gradeAction(Request $request, Realisation $realisation)
    {
        $identity = $this->get('security.token_storage')->getToken()->getUser()->getIdentity();

        $mark = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Mark')
            ->findBy([
                'realisation' => $realisation->getId(),
                'identity' => $identity
            ])
        ;
        if ($mark) {
            $this->addFlash('error', 'Vous avez déja noté cette réalisation.');

            return $this->redirectToRoute("public.realisation.show", ['realisation' => $realisation->getId()], 302);
        }

        $reaMarkDto = new RealisationMarkDto();
        $reaMarkDto->realisation = $realisation;
        $reaMarkDto->identity = $identity;

        // todo dire si l'user a déja noté cette réa
        $form = $this->createForm(MarkType::class, $reaMarkDto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reaMarkDto = $form->getData();

            $mark = $this
                ->get('app.realisation_mark.factory')
                ->create($reaMarkDto)
            ;

            $em = $this->getDoctrine()->getManager();
            $em->persist($mark);
            $em->flush();

            return $this->redirectToRoute("public.realisation.show", ['realisation' => $realisation->getId()], 302);
        }

        return $this->render(
            'AppBundle:Default:Realisation/grade.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }
}
