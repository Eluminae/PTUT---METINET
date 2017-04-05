<?php

namespace AppBundle\Services;

use AppBundle\Dtos\addJurorToCampaign;
use AppBundle\Models\Campaign;
use AppBundle\Models\Juror;
use AppBundle\Repositories\OrmJurorRepository;
use Symfony\Component\Config\Definition\Exception\Exception;

class JurorInvitationSender
{
    private $jurorRepository;
    private $identityRepository;

    public function __construct(OrmJurorRepository $jurorRepository, OrmIdentityRepository $identityRepository)
    {   
        $this->jurorRepository = $jurorRepository;
        $this->identityRepository = $identityRepository;
    }

    public function send(AddJurorToCapaign $addingJuror)
    {
        
    }
}
