<?php

namespace AppBundle\Models;

class Identity
{
    private $lastName;
    private $firstName;
    private $email;

    public function __construct(string $lastName, string $firstName, string $email)
    {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
