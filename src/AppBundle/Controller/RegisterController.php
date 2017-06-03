<?php

namespace AppBundle\Controller;

use AppBundle\Dtos\UserRegistration;
use AppBundle\Forms\InviteUserType;
use AppBundle\Forms\SignUpType;
use AppBundle\Models\Campaign;
use AppBundle\Models\Invitation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegisterController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function createInvitationForAdminsAction(Request $request)
    {
        $form = $this->invitationFormHandler($request, true);

        return $this->render(
            '@App/Admin/Administrator/inviteUser.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function createInvitationForJurorAction(Request $request, Campaign $campaign)
    {
        if ($campaign->isClosed()) {
            $this->addFlash('error', 'Vous ne pouvez plus inviter de juré car la campagne est terminée.');

            return $this->redirectToRoute("admin.campaign.show", ['campaign' => $campaign->getId()], 302);
        }

        $form = $this->invitationFormHandler($request, false, $campaign);

        return $this->render('@App/Admin/Campaign/inviteJuror.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request    $request
     * @param Invitation $invitation
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \LogicException
     * @throws \InvalidArgumentException
     *
     * @ParamConverter("invitation", options={"mapping": {"invitationToken": "invitationToken"}})
     */
    public function registerFromInvitationAction(Request $request, Invitation $invitation)
    {
        $userRegisterer = $this->get('app.services.user_registerer');

        $userRegistrationDto = new UserRegistration();
        $userRegistrationDto->email = $invitation->getEmail();
        $userRegistrationDto->role = $invitation->getRole();
        $userRegistrationDto->userObjectType = $userRegisterer->determineDataFromRole(
            $invitation->getRole(),
            'object'
        );

        $form = $this->createForm(SignUpType::class, $userRegistrationDto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userSignUp = $form->getData();

            $userRegisterer->verifyEmail($userSignUp->email);
            $savedUser = $userRegisterer->signUp($userSignUp, $invitation->getAssignedCampaigns());
            $provider = $userRegisterer->determineDataFromRole($userRegistrationDto->role, 'provider');


            $token = new UsernamePasswordToken(
                $savedUser,
                null,
                $provider,
                $savedUser->getRoles()
            );

            $em = $this->getDoctrine()->getManager();
            $em->remove($invitation);
            $em->flush();

            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_'.$provider, serialize($token));

            $this->addFlash('success', 'Inscription enregistré');

            return $this->redirectToRoute('public.homepage');
        }

        return $this->render(
            '@App/Default/registerFromInvitation.html.twig',
            ['form' => $form->createView(), 'invitation' => $invitation]
        );
    }

    /**
     * @param Request       $request
     * @param bool          $isAdmin
     * @param Campaign|null $campaign
     *
     * @return \Symfony\Component\Form\Form
     * @throws \LogicException
     */
    private function invitationFormHandler(Request $request, bool $isAdmin, Campaign $campaign = null)
    {
        $invitation = new Invitation();
        $invitation->setInvitationToken($this->get('app.uuid.generator')->generateUuid());

        if ($campaign) {
            $invitation->addAssignedCampaign($campaign);
        }

        $form = $this->createForm(InviteUserType::class, $invitation, ['isAdmin' => $isAdmin]);

        if (!$isAdmin) {
            $invitation->setRole('ROLE_JUROR');
        }

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
                        ['invitation' => $invitation, 'campaign' => $campaign]
                    ),
                    'text/html'
                );

            $this->get('mailer')->send($message);

            if ($invitationRepository->findOneByEmail($email)) {
                $this->addFlash('success', 'Une nouvelle invitation a été envoyé à l\'adresse email');
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
