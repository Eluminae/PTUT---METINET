<?php

namespace AppBundle\Services;

use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Models\Identity;
use AppBundle\Models\Realisation;
use AppBundle\Models\UtcDate;
use AppBundle\Repositories\OrmCampaignRepository;
use AppBundle\Services\FileFactory;
use Symfony\Component\Config\Definition\Exception\Exception;

class RealisationRegisterer
{
    private $campaingRepository;
    private $fileFactory;

    public function __construct(OrmCampaignRepository $campaingRepository, FileFactory $fileFactory)
    {
        $this->campaingRepository = $campaingRepository;
        $this->fileFactory = $fileFactory;
    }

    public function create(RealisationRegistration $realisationRegistration, $campaignId)
    {
        $campaign = $this->campaingRepository->findOneById($campaignId);
        if ($campaign === null) {
            throw new Exception('PAS DE CAMPAGNE');
        }

        $realisationFile = $this->fileFactory->createRealisationFile($realisationRegistration, $campaignId);

        return new Realisation(
            uniqid(),
            new UtcDate(uniqid(), new \DateTimeImmutable('now')),
            $realisationRegistration->name,
            $realisationFile,
            $campaign,
            $this->createCandidateFromIdentity($realisationRegistration->identity)
        );
    }

    private function createCandidateFromIdentity($identity)
    {   
        $candidates = array(
            new Identity(
                uniqid(),
                $identity['firstName'],
                $identity['lastName'],
                $identity['email']
            )
        );

        return $candidates;
    }
}
