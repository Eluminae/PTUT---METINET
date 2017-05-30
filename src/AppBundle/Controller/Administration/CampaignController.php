<?php

namespace AppBundle\Controller\Administration;

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

use ZipArchive;

class CampaignController extends Controller
{
    public function showAction(Request $request, string $campaignId)
    {
        $campaign = $this->get('app.campaign.repository')->findOneById($campaignId);

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

    public function deleteAction(Request $request, string $campaignId)
    {
        $campaign = $this->get('app.campaign.repository')->findOneById($campaignId);
        if (null === $campaign) {
            throw new \Exception(sprintf('Campaign %s not found.', $campaignId));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($campaign);
        $em->flush();

        return $this->redirectToRoute('admin.campaign.list');
    }

    public function updateAction(Request $request)
    {
        // todo
    }

    public function downloadAction(Request $request, string $campaignId)
    {
        $campaign = $this->get('app.campaign.repository')->findOneById($campaignId);
        if (null === $campaign) {
            throw new \Exception(sprintf('Realisation %s not found.', $campaignId));
        }

        $realisations = $this->get('app.realisation.repository')->findByCampaign($campaign);

        $files = array();
        foreach ($realisations as $realisation) {
            $files[] = $realisation->getFilePath();
        }

        $zip = new ZipArchive();
        $zipName = $campaign->getName().'_'.$campaign->getId().'.zip';
        $zip->open($zipName, ZipArchive::CREATE);
        foreach ($files as $file) {
            $zip->addFromString(basename($file), file_get_contents($file));
        }
        $zip->close();

        return $this->file($zipName);
    }
}
