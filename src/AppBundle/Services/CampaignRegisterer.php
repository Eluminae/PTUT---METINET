<?php

namespace AppBundle\Services;

use AppBundle\Dtos\CampaignRegistration;
use AppBundle\Models\Campaign;
use AppBundle\Models\Identity;
use AppBundle\Models\UtcDate;
use AppBundle\Repositories\OrmIdentityRepository;
use Symfony\Component\Config\Definition\Exception\Exception;

class CampaignRegisterer
{
    private $identityRepository;

    public function __construct(OrmIdentityRepository $identityRepository)
    {   
        $this->identityRepository = $identityRepository;
    }

    public function create(CampaignRegistration $campaignRegistration, $userId)
    {
        $file = $campaignRegistration->image;
        $fileName = sprintf('%s.%s', md5(uniqid()), $file->guessExtension());
        $file->move(
            Campaign::filePath,
            $fileName
        );

        $user = $this->identityRepository->findOneById($userId);

        return new Campaign(
            uniqid(),
            new UtcDate(uniqid(), \DateTimeImmutable::createFromMutable($campaignRegistration->endDate)),
            new UtcDate(uniqid(), \DateTimeImmutable::createFromMutable($campaignRegistration->beginningDate)),
            $campaignRegistration->name,
            $campaignRegistration->description,
            $fileName,
            $user
        );
    }
}
