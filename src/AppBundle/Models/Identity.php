<?php

namespace AppBundle\Models;

class Identity
{
    private $id;
    private $lastName;
    private $firstName;
    private $email;

    public function __construct(string $id, string $lastName, string $firstName, string $email)
    {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
    }

    public function getId()
    {
        return $this->id;
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
