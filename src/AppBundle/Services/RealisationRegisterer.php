<?php

namespace AppBundle\Services;

use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Models\Identity;
use AppBundle\Models\Realisation;
use AppBundle\Models\UtcDate;
use AppBundle\Repositories\OrmCampaignRepository;

class RealisationRegisterer
{
    private $campaingRepository;

    public function __construct(OrmCampaignRepository $campaingRepository)
    {
        $this->campaingRepository = $campaingRepository;
    }

    public function create(RealisationRegistration $realisationRegistration, $campaignId)
    {
        return new Realisation(
            uniqid(),
            new UtcDate(uniqid(), new \DateTimeImmutable('now')),
            $realisationRegistration->name,
            new File(uniqid(), 'zip', $realisationRegistration->file),
            $this->campaingRepository->findOneById($this->campaignId),
            $this->createCandidateFromIdentity($realisationRegistration->identity)
        );
    }

    private function createCandidateFromIdentity($identity)
    {   
        $candidates = array(
            new Identity(
                uniqid(),
                $identity->firstName,
                $identity->lastName,
                $identity->email
            )
        );

        return $candidates;
    }
}
