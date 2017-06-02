<?php

namespace AppBundle\Controller;

use AppBundle\Dtos\UserRegistration;
use AppBundle\Forms\SignInType;
use AppBundle\Forms\SignUpType;
use AppBundle\Models\Identity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $form = $this->createForm(SignInType::class);

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            '@App/Admin/signIn.html.twig',
            [
                'lastUsername' => $lastUsername,
                'error' => $error,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     */
    public function logoutAction(Request $request)
    {
    }

    /**
     * @param $name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \LogicException
     */
    public function editProfileAction(Request $request)
    {
        // todo : This logic may be moved into a standalone service

        /** @var Identity $currentUserIdentity */
        $currentUser = $this->getUser();
        $currentUserIdentity = $this->getUser()->getIdentity();

        $userDto = new UserRegistration();
        $userDto->email = $currentUserIdentity->getEmail();
        $userDto->firstName = $currentUserIdentity->getFirstName();
        $userDto->lastName = $currentUserIdentity->getLastName();

        $form = $this->createForm(SignUpType::class, $userDto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUserData = $form->getData();

            $userRegisterer = $this->get('app.services.user_registerer');

            $currentUserIdentity->setEmail($newUserData->email);
            $currentUserIdentity->setFirstName($newUserData->firstName);
            $currentUserIdentity->setLastName($newUserData->lastName);

            if (null !== $newUserData->password) {
                $password = $userRegisterer->encodePasswordFromPlain($newUserData->password);
                $currentUser->setSalt($password['salt']);
                $currentUser->setPassword($password['encodedPassword']);
            }

            $userRegisterer->authenticateUser($this->getUser());

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Profil mis à jour');
        }

        return $this->render('@App/Admin/editProfile.html.twig', ['form' => $form->createView()]);
    }

    public function listAction(Request $request)
    {
        $campaignAdministrators = $this->get('app.campaign_administrator.repository')->findAll();
        $jurors = $this->get('app.juror.repository')->findAll();
        $administrators = $this->get('app.administrator.repository')->findAll();

        $users = array_merge_recursive($campaignAdministrators, $jurors);
        $users = array_merge_recursive($users, $administrators);

        return $this->render('@App/Admin/listUsers.html.twig', [
            'users' => $users
        ]);
    }
}