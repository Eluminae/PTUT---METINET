<?php

namespace AppBundle\Controller;

use AppBundle\Forms\InviteUserType;
use AppBundle\Models\Invitation;
use AppBundle\Models\UserInvitation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class RegisterController extends Controller
{
    public function createInvitationForAdminsAction(Request $request)
    {
        // form with option of roles for admin
        // todo add for juror assignation from admin

        /** @var UserInterface */
        $currentUser = $this->getUser()->

        $form = $this->createForm(InviteUserType::class, new Invitation(), ['isAdmin' => ])

            if ()

                // handle -> get a unique ID to invitation token
    }

    public function createInvitationForJurorAction(Request $request)
    {
        // Set assign camapaign
    }


    public function registerFromInvitationAction(string $invitationToken)
    {

        // handle
        return $this->render('');
    }
}
