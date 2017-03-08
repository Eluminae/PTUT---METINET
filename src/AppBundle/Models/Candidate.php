<?php

namespace AppBundle\Models;

use AppBundle\Models\Identity;

class Candidate
{
    private $id;
    private $identity;

    public function __construct(string $id, Identity $identity)
    {
        $this->id = $id;
        $this->identity = $identity;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdentity()
    {
        return $this->identity;
    }
}
