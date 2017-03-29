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

    public function __construct(OrmCampaignRepository $campaingRepository)
    {
        $this->campaingRepository = $campaingRepository;
    }

    public function create(RealisationRegistration $realisationRegistration, $campaignId)
    {
        $campaign = $this->campaingRepository->findOneById($campaignId);
        if ($campaign === null) {
            throw new Exception('PAS DE CAMPAGNE');
        }

        $file = $realisationRegistration->file;
        $fileName = sprintf('%s.%s', md5(uniqid()), $file->guessExtension());
        $file->move(
            'realisationFiles',
            $fileName
        );

        return new Realisation(
            uniqid(),
            new UtcDate(uniqid(), new \DateTimeImmutable('now')),
            $realisationRegistration->name,
            $fileName,
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
