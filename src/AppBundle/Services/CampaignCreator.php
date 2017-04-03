<?php

namespace AppBundle\Services;

use AppBundle\Dtos\CampaignCreation;
use AppBundle\Models\Campaign;
use AppBundle\Models\Identity;
use AppBundle\Models\UtcDate;
use AppBundle\Repositories\OrmIdentityRepository;
use Symfony\Component\Config\Definition\Exception\Exception;

class CampaignCreator
{
    private $identityRepository;

    public function __construct(OrmIdentityRepository $identityRepository)
    {   
        $this->identityRepository = $identityRepository;
    }

    public function create(CampaignCreation $campaignCreation, $userId)
    {
        $file = $campaignCreation->image;
        $fileName = sprintf('%s.%s', md5(uniqid()), $file->guessExtension());
        $file->move(
            Campaign::filePath,
            $fileName
        );

        $user = $this->identityRepository->findOneById($userId);

        return new Campaign(
            uniqid(),
            new UtcDate(uniqid(), \DateTimeImmutable::createFromMutable($campaignCreation->endDate)),
            new UtcDate(uniqid(), \DateTimeImmutable::createFromMutable($campaignCreation->beginningDate)),
            $campaignCreation->name,
            $campaignCreation->description,
            $fileName,
            $user
        );
    }
}
