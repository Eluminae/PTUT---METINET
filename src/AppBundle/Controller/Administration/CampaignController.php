<?php

namespace AppBundle\Controller\Administration;

use AppBundle\Dtos\AddJurorToCampaign;
use AppBundle\Dtos\RealisationMarkDto;
use AppBundle\Forms\AddJurorToCampaignType;
use AppBundle\Forms\CampaignCreationType;
use AppBundle\Forms\GradeCampaignType;
use AppBundle\Models\UtcDate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Dtos\CampaignCreation;
use AppBundle\Forms\CampaignCreationType;
use AppBundle\Models\Campaign;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CampaignController extends Controller
{
    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     *
     */
    public function showAction(Request $request, Campaign $campaign)
    {
        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign);

        $jurors = $campaign->getJurors();

        return $this->render(
            'AppBundle:Admin:Campaign/show.html.twig',
            [
                'campaign' => $campaign,
                'jurors' => $jurors,
                'realisations' => $realisations,
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
        $campaignsNeedReview = $this->get('app.campaign.repository')->findByStatus(Campaign::TO_BE_REVIEWED);
        $campaignsApproved = $this->get('app.campaign.repository')->findByStatus(Campaign::ACCEPTED);

        return $this->render(
            'AppBundle:Admin:Campaign/list.html.twig',
            [
                'campaignsNeedReview' => $campaignsNeedReview,
                'campaignsApproved' => $campaignsApproved,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
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
            'AppBundle:Admin:Campaign/create.html.twig',
            [
                'campaignCreationForm' => $form->createView(),
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
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function deleteAction(Request $request, Campaign $campaign)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($campaign);
        $em->flush();

        $this->addFlash('success', 'La campagne a bien été supprimé');

        return $this->redirectToRoute('admin.campaign.list');
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     */
    public function updateAction(Request $request, Campaign $campaign)
    {
        // todo
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     *
     * @throws \LogicException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \InvalidArgumentException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function approveAction(Campaign $campaign)
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            throw new AccessDeniedException("Sorry, you're not a administrator.");
        }

        $campaign->approveCampaign();
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'La campagne a bien été approuvé');

        return $this->redirectToRoute('admin.campaign.list');
    }

    public function gradeAction(Request $request, Campaign $campaign)
    {
        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign);

        $identity = $this->get('security.token_storage')->getToken()->getUser()->getIdentity();

        // $mark = $this
        //     ->getDoctrine()
        //     ->getRepository('AppBundle:Mark')
        //     ->findBy([
        //         'realisation' => $realisation->getId(),
        //         'identity' => $identity
        //     ])
        // ;
        // if ($mark) {
        //     $this->addFlash('error', 'Vous avez déja noté cette réalisation.');

        //     return $this->redirectToRoute("public.realisation.show", ['realisation' => $realisation->getId()], 302);
        // }

        // $reaMarkDto = new RealisationMarkDto();
        // $reaMarkDto->realisation = $realisation;
        // $reaMarkDto->identity = $identity;
        
        $markDtoTable = [];

        foreach ($realisations as $realisation) {
            $markDtoTemp = new RealisationMarkDto();
            $markDtoTemp->idRealisationisation = $realisation->getId();
            $markDtoTemp->identity = $identity;
            
            $markDtoTable[] = $markDtoTemp;
        }


        $form = $this->createForm(GradeCampaignType::class, $markDtoTable);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reaMarkDto = $form->getData();

            // $mark = $this
            //     ->get('app.realisation_mark.factory')
            //     ->create($reaMarkDto)
            // ;

            // $em = $this->getDoctrine()->getManager();
            // $em->persist($mark);
            // $em->flush();

            return $this->redirectToRoute("public.campaign.show", ['campaign' => $campaign->getId()], 302);
        }

        return $this->render(
            'AppBundle:Default:Campaign/grade.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }
}
