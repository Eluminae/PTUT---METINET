<?php

namespace AppBundle\Models;

use AppBundle\Models\Identity;

class Administrator
{
    private $identity;
    private $password;

    public function __construct(Identity $identity, string $password)
    {
        $this->identity = $identity;
        $this->password = $password;
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
