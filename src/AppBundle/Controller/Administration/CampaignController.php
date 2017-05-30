<?php

namespace AppBundle\Controller\Administration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Config\Definition\Exception\Exception;
use AppBundle\Dtos\AddJurorToCampaign;
use AppBundle\Forms\AddJurorToCampaignType;

use AppBundle\Forms\CampaignCreationType;
use AppBundle\Dtos\CampaignCreation;
use AppBundle\Models\Campaign;
use AppBundle\Models\UtcDate;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ZipArchive;

class CampaignController extends Controller
{
    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param string $campaignId
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
        $campaignsNeedReview = $this->get('app.campaign.repository')->findByReview(false);
        $campaignsApproved = $this->get('app.campaign.repository')->findByReview(true);

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
     * @param Request $request
     * @param string  $campaignId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
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
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     *
     */
    public function downloadAction(Request $request, Campaign $campaign)
    {
        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign);

        $files = array();
        foreach ($realisations as $realisation) {
            $files[] = $realisation->getFilePath();
        }

        $zip = new ZipArchive();
        $zipName = 'realisationZipFiles/'.$campaign->getName().'_'.$campaign->getId().'.zip';
        $zip->open($zipName, ZipArchive::CREATE);
        foreach ($files as $file) {
            $zip->addFromString(basename($file), file_get_contents($file));
        }
        $zip->close();

        return $this->file($zipName);
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
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            throw new AccessDeniedException("Sorry, you're not a administrator.");
        }

        $campaign->approveCampaign();
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'La campagne a bien été approuvé');

        return $this->redirectToRoute('admin.campaign.list');
    }
}
