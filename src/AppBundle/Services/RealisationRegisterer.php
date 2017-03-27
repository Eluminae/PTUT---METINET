<?php

namespace AppBundle\Services;

use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Models\Identity;
use AppBundle\Models\Realisation;
use AppBundle\Models\UtcDate;

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
            $this->campaingRepository->getOneById($this->campaignId)
            $this->createCandidatesFromIdentities($realisationRegistration->identities)
        );
    }

    private function createCandidatesFromIdentities($identities)
    {
        $candidates = array();

        foreach ($candidates as $candidate) {
            $candidates[] = new Identity(
                uniqid(),
                $candidate->firstName,
                $candidate->lastName,
                $candidate->email
            );
        }

        return $candidates;
    }
}
