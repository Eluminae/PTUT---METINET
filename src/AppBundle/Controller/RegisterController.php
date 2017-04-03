<?php

namespace AppBundle\Controller;

use AppBundle\Dtos\UserRegistration;
use AppBundle\Forms\InviteUserType;
use AppBundle\Forms\SignUpType;
use AppBundle\Models\Campaign;
use AppBundle\Models\Juror;
use AppBundle\Models\Invitation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends Controller
{
    public function createInvitationForAdminsAction(Request $request)
    {
        // todo add for juror assignation from admin

        $form = $this->invitationFormHandler($request, true);

        return $this->render('@App/Admin/inviteUser.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createInvitationForJurorAction(Request $request, Campaign $campaign)
    {
        $form = $this->invitationFormHandler($request, false, $campaign);

        return $this->render('@App/CampaignAdmin/inviteJuror.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Invitation $invitation
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("invitation", options={"mapping": {"invitationToken": "invitationToken"}})
     */
    public function registerFromInvitationAction(Request $request, Invitation $invitation)
    {
        // todo : Handle assignations to multiples camapaigns invitations without juror account

        $userRegistrationDto = new UserRegistration();
        $userRegistrationDto->email = $invitation->getEmail();
        $userRegistrationDto->role = $invitation->getRole();

        $form = $this->createForm(SignUpType::class, new UserRegistration());



        // todo !!!!
//        if ($invitation->getAssignedCampaign()) {
//            /** @var Juror $userTypeEntity */
//            $userTypeEntity->addCampaign($invitation->getAssignedCampaign());
//        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userSignUp = $form->getData();

            $this->get('app.services.user_registerer')->signUp($userSignUp);
        }



        return $this->render('@App/Juror/registerFromInvitation.html.twig', ['form' => $form->createView(), 'email' => $invitation->getEmail()]);
    }

    private function invitationFormHandler(Request $request, bool $isAdmin, Campaign $campaign = null)
    {
        $invitation = new Invitation();
        $invitation->setInvitationToken($this->get('app.uudi.generator')->generateUuid());

        if ($campaign) {
            $invitation->setAssignedCampaign($campaign);
        }

        $form = $this->createForm(InviteUserType::class, $invitation, ['isAdmin' => $isAdmin]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()->getEmail();
            $identityRepository = $this->getDoctrine()->getRepository('AppBundle:Identity');
            $invitationRepository = $this->getDoctrine()->getRepository('AppBundle:Invitation');

            if ($identityRepository->findOneByEmail($email)) {
                // todo : Do a route which redirect to the right dashboard (/admin, /juror, etc)
                $this->addFlash('error', 'Cette adresse e-mail est déjà utilisé');
                $this->redirectToRoute('public.homepage');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($invitation);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Invitation utilisateur - Work-Competition')
                ->setFrom('invitation@work-competition.co')
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        '@App/Email/inviteUser.html.twig',
                        ['invitation' => $invitation]
                    ),
                    'text/html'
                );

            $this->get('mailer')->send($message);

            if ($invitationRepository->findOneByEmail($email)) {
                $this->addFlash('error', 'Une nouvelle invitation a été envoyé à l\'adresse email');
            } else {
                $this->addFlash(
                    'success',
                    'L\'utilisateur a bien été ajouté. Un e-mail d\'invitation vient de lui être envoyé.'
                );
            }
        }

        return $form;
    }
}
