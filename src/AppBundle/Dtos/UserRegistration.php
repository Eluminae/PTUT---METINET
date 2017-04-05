<?php
/**
 * Created by PhpStorm.
 * User: corentinbouix
 * Date: 03/04/2017
 * Time: 16:30
 */

namespace AppBundle\Dtos;


class UserRegistration extends IdentityRegistration
{
    public $password;
    public $role;
    public $userObjectType;
    public $campaign;
}