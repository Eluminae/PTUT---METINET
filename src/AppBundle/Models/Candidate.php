<?php

namespace AppBundle\Models;

use AppBundle\Models\Identity;

class Candidate
{
    private $identity;

    public function __construct(Identity $identity)
    {
        $this->identity = $identity;
    }

    public function getIdentity()
    {
        return $this->identity;
    }
}
