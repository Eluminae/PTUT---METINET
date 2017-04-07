<?php

namespace AppBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Config\Definition\Exception\Exception;
use AppBundle\Dtos\AddJurorToCampaign;
use AppBundle\Forms\AddJurorToCampaignType;

use AppBundle\Dtos\CampaignCreation;
use AppBundle\Forms\CampaignCreationType;
use AppBundle\Models\Campaign;
use AppBundle\Models\UtcDate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CampaignController extends Controller
{
    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Campaign $campaign)
    {
        if ($campaign === null) {
            throw new Exception("Pas de campage avec cet id");
        }

        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign);

        $jurors = $campaign->getJurors();

        return $this->render(
            'AppBundle:Admin:Campaign/show.html.twig', [
                'campaign' => $campaign,
                'jurors' => $jurors,
                'realisations' =>$realisations
            ]
        );
    }

    public function listAction(Request $request)
    {
        $campaigns = $this->get('app.campaign.repository')->findAll();

        return $this->render(
            'AppBundle:Admin:Campaign/list.html.twig', [
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
            'AppBundle:Admin:Campaign/create.html.twig', [
                'campaignCreationForm' => $form->createView()
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
    public function deleteAction(Request $request, Campaign $campaign)
    {
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
