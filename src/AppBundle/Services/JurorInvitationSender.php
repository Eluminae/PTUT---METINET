<?php

namespace AppBundle\Services;

use AppBundle\Dtos\AddJurorToCampaign;
use AppBundle\Repositories\OrmJurorRepository;
use AppBundle\Repositories\OrmIdentityRepository;

class JurorInvitationSender
{
    private $jurorRepository;
    private $identityRepository;

    public function __construct(OrmJurorRepository $jurorRepository, OrmIdentityRepository $identityRepository)
    {
        $this->jurorRepository = $jurorRepository;
        $this->identityRepository = $identityRepository;
    }

    public function send(AddJurorToCampaign $addingJuror)
    {
    }
}
