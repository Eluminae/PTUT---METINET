<?php

namespace AppBundle\Controller\Administration;

use AppBundle\Dtos\CampaignCreation;
use AppBundle\Dtos\RealisationMarkDto;
use AppBundle\Forms\CampaignCreationType;
use AppBundle\Forms\GradeCampaignType;
use AppBundle\Models\Campaign;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ZipArchive;

class CampaignController extends Controller
{
    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     */
    public function showAction(Request $request, Campaign $campaign)
    {
        $user = $this->getUser();
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

        $user = $this->getUser();
        foreach ($campaignsApproved as $key => $campaignApproved) {
            if (
                false === $this->get('app.user.authorization_checker')->isAllowedToShowCampaign($user, $campaignApproved)
            ) {
                unset($campaignsApproved[$key]);
            }
        }

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
     *
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

            $this->addFlash('success', 'La campagne a bien été crée. Elle doit maintenant êtra validée par un administrateur.');

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
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function deleteAction(Request $request, Campaign $campaign)
    {
        $user = $this->getUser();
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
     *
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

     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function gradeAction(Request $request, Campaign $campaign)
    {
        if (false === $campaign->isOver()) {
            $this->addFlash('error', 'Vous ne pourrez évaluer cette campagne que lorsqu\'elle sera terminée.');

            return $this->redirectToRoute('admin.campaign.show', ['campaign' => $campaign->getId()], 302);
        }

        $user = $this->getUser();
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
                    'identity' => $identity,
                ]
            )
        ;
        if ($mark) {
            $this->addFlash('error', 'Vous avez déja évalué cette campagne.');

            return $this->redirectToRoute('admin.campaign.show', ['campaign' => $campaign->getId()], 302);
        }

        $markDtos = ['realisations' => []];
        foreach ($realisations as $realisation) {
            $markDtoTemp = new RealisationMarkDto();
            $markDtoTemp->realisation = $realisation;
            $markDtoTemp->identity = $identity;

            $markDtos['realisations'][$realisation->getId()] = $markDtoTemp;
        }

        $form = $this->createForm(GradeCampaignType::class, $markDtos);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reaMarkDtos = $form->getData()['realisations'];

            $em = $this->getDoctrine()->getManager();
            foreach ($reaMarkDtos as $reaMarkDto) {
                $mark = $this
                    ->get('app.realisation_mark.factory')
                    ->create($reaMarkDto)
                ;

                $em->persist($mark);
            }

            $em->flush();

            $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign);
            foreach ($realisations as $realisation) {
                $marks = $this->get('app.mark.repository')->findByRealisation($realisation);
                $averageMark = 0;
                foreach ($marks as $mark) {
                    $averageMark += $mark->getValue();
                }
                $averageMark /= sizeof($marks);
                $realisation->updateAverageMark($averageMark);
            }
            $em->flush();

            $marks = 0;
            foreach ($realisations as $realisation) {
                $marks += count($this->get('app.mark.repository')->findByRealisation($realisation));
            }
            if ($marks === count($realisations) * count($campaign->getJurors())) {
                if ($campaign->isResultsPublic()) {
                    $campaign->publishResults();
                } else {
                    $campaign->close();
                }
            }
            $em->flush();

            $this->addFlash('success', 'Vous avez évalué cette campagne.');

            return $this->redirectToRoute('admin.campaign.show', ['campaign' => $campaign->getId()], 302);
        }

        return $this->render(
            'AppBundle:Admin:Campaign/grade.html.twig', [
                'form' => $form->createView(),
                'notation' => $campaign->getNotation(),
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
    public function downloadAction(Request $request, Campaign $campaign)
    {
        $user = $this->getUser();
        if (
            false === $this->get('app.user.authorization_checker')->isAllowedToShowCampaign($user, $campaign)
        ) {
            throw new AccessDeniedException('Vous n\'êtes pas authorisé à administrer cette campagne');
        }

        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign);

        if (!is_dir('realisationZipFiles')) {
            mkdir('realisationZipFiles', 0775, true);
        }

        if (!empty($realisations)) {
            $zip = new ZipArchive();
            $zipName = 'realisationZipFiles/'.$campaign->getName().'_'.$campaign->getId().'.zip';
            $zip->open($zipName, ZipArchive::CREATE);
            foreach ($realisations as $realisation) {
                $file = $realisation->getFilePath();
                $zip->addFromString(basename($file), file_get_contents($file));
            }
            $zip->close();

            return $this->file($zipName);
        }

        return $this->redirectToRoute('admin.campaign.show', ['campaign' => $campaign->getId()]);
    }
}
