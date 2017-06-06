<?php

namespace AppBundle\Dtos;

class UserRegistration extends IdentityRegistration
{
    public $password;
    public $role;
    public $userObjectType;
    public $campaign;
    public $officialGroup;
}
