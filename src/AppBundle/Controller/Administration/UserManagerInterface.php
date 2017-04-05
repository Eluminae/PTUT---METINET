<?php
/**
 * Created by PhpStorm.
 * User: corentinbouix
 * Date: 29/03/2017
 * Time: 16:13
 */

namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\Request;

interface UserManagerInterface
{
    public function loginAction(Request $request);
    public function logoutAction(Request $request);
}