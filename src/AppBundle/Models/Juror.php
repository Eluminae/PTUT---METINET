<?php

namespace AppBundle\Models;

use AppBundle\Models\Campaign;
use AppBundle\Models\Identity;
use AppBundle\Models\Password;

class Juror
{
    private $id;
    private $identity;
    private $password;
    private $campaign;

    public function __construct(string $id, Identity $identity, Password $password, Campaign $campaign)
    {
        $this->id = $id;
        $this->identity = $identity;
        $this->password = $password;
        $this->campaign = $campaign;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getCampaign()
    {
        return $this->campaign;
    }
}
