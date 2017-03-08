<?php

namespace AppBundle\Models;

use AppBundle\Models\Identity;

class Administrator
{
    private $id;
    private $identity;
    private $password;

    public function __construct(string $id, Identity $identity, string $password)
    {
        $this->id = $id;
        $this->identity = $identity;
        $this->password = $password;
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
}
