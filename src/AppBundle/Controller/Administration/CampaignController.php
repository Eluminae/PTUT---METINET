<?php

namespace AppBundle\Controller\Administration;

use AppBundle\Dtos\AddJurorToCampaign;
use AppBundle\Dtos\CampaignCreation;
use AppBundle\Dtos\RealisationMarkDto;
use AppBundle\Forms\AddJurorToCampaignType;
use AppBundle\Forms\CampaignCreationType;
use AppBundle\Forms\GradeCampaignType;
use AppBundle\Models\Campaign;
use AppBundle\Models\Juror;
use AppBundle\Models\UtcDate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (
            false === $this->get('app.user.authorization_checker')->isAllowedToShowCampaign($user, $campaign)
        ) {
            throw new AccessDeniedException('Vous n\'êtes pas authorisé à administrer cette campagne');
        }

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
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (
            false === $this->get('app.user.authorization_checker')->isAllowedToDeleteCampaign($user, $campaign)
        ) {
            throw new AccessDeniedException('Vous n\'êtes pas authorisé à supprimer cette campagne');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($campaign);
        $em->flush();

        $this->addFlash('success', 'La campagne a bien été supprimé');

        return $this->redirectToRoute('admin.campaign.list');
    }

    /**
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
        $campaign->approveCampaign();
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'La campagne a bien été approuvé');

        return $this->redirectToRoute('admin.campaign.list');
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")

     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function gradeAction(Request $request, Campaign $campaign)
    {
        if (false === $campaign->isClosed()) {
            $this->addFlash('error', 'Vous ne pourrez évaluer cette campagne que lorsqu\'elle sera terminée.');

            return $this->redirectToRoute("admin.campaign.show", ['campaign' => $campaign->getId()], 302);
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (
            false === $this->get('app.user.authorization_checker')->isAllowedToGradeCampaign($user, $campaign)
        ) {
            throw new AccessDeniedException('Vous n\'êtes pas authorisé à évaluer cette campagne');
        }

        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign);

        $identity = $user->getIdentity();

        $mark = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Mark')
            ->findBy(
                [
                    'realisation' => $realisations[0]->getId(),
                    'identity' => $identity
                ]
            )
        ;
        if ($mark) {
            $this->addFlash('error', 'Vous avez déja évalué cette campagne.');

            return $this->redirectToRoute("admin.campaign.show", ['campaign' => $campaign->getId()], 302);
        }

        $markDtoTable = ['realisations' => []];
        foreach ($realisations as $realisation) {
            $markDtoTemp = new RealisationMarkDto();
            $markDtoTemp->realisation = $realisation;
            $markDtoTemp->identity = $identity;

            $markDtoTable['realisations'][$realisation->getId()] = $markDtoTemp;
        }

        $form = $this->createForm(GradeCampaignType::class, $markDtoTable);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reaMarkDtoTable = $form->getData()['realisations'];

            $em = $this->getDoctrine()->getManager();
            foreach ($reaMarkDtoTable as $key => $reaMarkDto) {
                $mark = $this
                    ->get('app.realisation_mark.factory')
                    ->create($reaMarkDto)
                ;

                $em->persist($mark);
            }

            $em->flush();

            foreach ($this->get('app.realisation.repository')->findByCampaign($campaign) as $realisation) {
                $marks = $this->get('app.mark.repository')->findByRealisation($realisation);
                $averageMark = 0;
                foreach ($marks as $mark) {
                    $averageMark += $mark->getValue();
                }
                $averageMark /= sizeof($marks);
                $realisation->updateAverageMark($averageMark);
            }
            $em->flush();

            $this->addFlash('success', 'Vous avez évalué cette campagne.');

            return $this->redirectToRoute("admin.campaign.show", ['campaign' => $campaign->getId()], 302);
        }

        return $this->render(
            'AppBundle:Admin:Campaign/grade.html.twig', [
                'form' => $form->createView(),
                'notation' => $campaign->getNotation(),
            ]
        );
    }
}
