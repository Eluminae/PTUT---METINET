<?php

namespace AppBundle\Services;

use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Models\Identity;
use AppBundle\Models\Realisation;
use AppBundle\Models\UtcDate;
use AppBundle\Repositories\OrmCampaignRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\File;

class RealisationRegisterer
{
    private $campaingRepository;
    /** @var UuidGenerator */
    private $uuidGenerator;

    public function __construct(OrmCampaignRepository $campaingRepository, UuidGenerator $uuidGenerator)
    {
        $this->campaingRepository = $campaingRepository;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function create(RealisationRegistration $realisationRegistration, $campaignId)
    {
        $campaign = $this->campaingRepository->findOneById($campaignId);
        if ($campaign === null) {
            throw new Exception('PAS DE CAMPAGNE');
        }

        /** @var File $file */
        $file = $realisationRegistration->file;
        $fileName = sprintf('%s.%s', $this->uuidGenerator->generateUuid(), $file->guessExtension());
        $file->move(
            Realisation::filePath,
            $fileName
        );

        return new Realisation(
            $this->uuidGenerator->generateUuid(),
            new UtcDate($this->uuidGenerator->generateUuid(), new \DateTimeImmutable('now')),
            $realisationRegistration->name,
            $fileName,
            $campaign,
            $this->createCandidateFromIdentity($realisationRegistration->identity)
        );
    }

    private function createCandidateFromIdentity($identity)
    {
        $candidates = [
            new Identity(
                $this->uuidGenerator->generateUuid(),
                $identity['firstName'],
                $identity['lastName'],
                $identity['email']
            )
        ];

        return $candidates;
    }
}
