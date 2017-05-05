<?php

namespace AppBundle\Controller\Administrator;


use Symfony\Component\HttpFoundation\Request;

interface UserManagerInterface
{
    public function loginAction(Request $request);
    public function logoutAction(Request $request);
}
