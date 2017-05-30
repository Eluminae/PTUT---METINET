<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Config\Definition\Exception\Exception;

use AppBundle\Forms\CampaignCreationType;
use AppBundle\Dtos\CampaignCreation;
use AppBundle\Forms\CampaignCreationType;
use AppBundle\Models\Campaign;
use AppBundle\Models\UtcDate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use ZipArchive;

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
        return $this->render(
            'AppBundle:Default:Campaign/show.html.twig', [
                'campaign' => $campaign
            ]
        );
    }

    public function listAction(Request $request)
    {
        $campaignsApproved = $this->get('app.campaign.repository')->findByReview(true);

        return $this->render(
            'AppBundle:Default:Campaign/list.html.twig', [
                'campaigns' => $campaignsApproved
            'AppBundle:Default:Campaign/list.html.twig', [
                'campaigns' => $campaigns
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
        }

        return $this->file($zipName);
    }
}
