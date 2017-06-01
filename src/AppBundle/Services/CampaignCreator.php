<?php

namespace AppBundle\Services;

use AppBundle\Dtos\CampaignCreation;
use AppBundle\Models\Campaign;
use AppBundle\Models\Identity;
use AppBundle\Models\UtcDate;
use AppBundle\Models\Notation;
use AppBundle\Repositories\OrmIdentityRepository;
use Symfony\Component\Config\Definition\Exception\Exception;

class CampaignCreator
{
    private $identityRepository;
    /** @var UuidGenerator */
    private $uuidGenerator;

    public function __construct(OrmIdentityRepository $identityRepository, UuidGenerator $uuidGenerator)
    {
        $this->identityRepository = $identityRepository;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function create(CampaignCreation $campaignCreation, $userId)
    {
        $file = $campaignCreation->image;
        $fileName = sprintf('%s.%s', md5(uniqid()), $file->guessExtension());
        $file->move(
            Campaign::FILE_PATH,
            $fileName
        );

        $user = $this->identityRepository->findOneById($userId);

        $notation = new Notation(
            $this->uuidGenerator->generateUuid(),
            $campaignCreation->notation['markType'], 
            $campaignCreation->notation['markTypeNumber']
        );

        return new Campaign(
            $this->uuidGenerator->generateUuid(),
            new UtcDate($this->uuidGenerator->generateUuid(), \DateTimeImmutable::createFromMutable($campaignCreation->endDate)),
            new UtcDate($this->uuidGenerator->generateUuid(), \DateTimeImmutable::createFromMutable($campaignCreation->beginningDate)),
            $campaignCreation->name,
            $campaignCreation->description,
            $fileName,
            $user,
            $notation
        );
    }
}
