<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends Controller
{
    public function createInvitationAction(Request $request)
    {

    }

    public function registerFromInvitationAction(string $invitationToken)
    {
        // handle
        return $this->render('');
    }
}
