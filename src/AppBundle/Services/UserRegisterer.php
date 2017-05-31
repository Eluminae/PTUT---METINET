<?php

namespace AppBundle\Services;

use AppBundle\Dtos\UserRegistration;
use AppBundle\Models\Identity;
use AppBundle\Models\Juror;
use AppBundle\Repositories\OrmAdministratorRepository;
use AppBundle\Repositories\OrmCampaignAdministratorRepository;
use AppBundle\Repositories\OrmCampaignRepository;
use AppBundle\Repositories\OrmJurorRepository;
use AppBundle\Services\UuidGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserRegisterer
{
    /** @var PasswordEncoderInterface */
    private $passwordEncoder;
    /** @var UuidGenerator */
    private $uuidGenerator;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var OrmAdministratorRepository */
    private $administratorRepository;
    /** @var OrmCampaignAdministratorRepository */
    private $campaignAdministratorRepository;
    /** @var OrmJurorRepository */
    private $jurorRepository;
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var SessionInterface */
    private $session;

    /**
     * UserRegisterer constructor.
     *
     * @param PasswordEncoderInterface   $passwordEncoder
     * @param UuidGenerator              $uuidGenerator
     * @param EntityManagerInterface     $entityManager
     * @param OrmAdministratorRepository $administratorRepository
     * @param OrmCampaignAdministratorRepository   $campaignAdministratorRepository
     * @param OrmJurorRepository         $jurorRepository
     * @param TokenStorageInterface      $tokenStorage
     * @param SessionInterface           $session
     *
     */
    public function __construct(
        PasswordEncoderInterface $passwordEncoder,
        UuidGenerator $uuidGenerator,
        EntityManagerInterface $entityManager,
        OrmAdministratorRepository $administratorRepository,
        OrmCampaignAdministratorRepository $campaignAdministratorRepository,
        OrmJurorRepository $jurorRepository,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->uuidGenerator = $uuidGenerator;
        $this->entityManager = $entityManager;
        $this->administratorRepository = $administratorRepository;
        $this->campaignAdministratorRepository = $campaignAdministratorRepository;
        $this->jurorRepository = $jurorRepository;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    /**
     * @param UserRegistration $userRegistrationDto
     *
     * @return UserInterface
     */
    public function signUp(UserRegistration $userRegistrationDto)
    {
        $password = $this->encodePasswordFromPlain($userRegistrationDto->password);

        $identity = new Identity(
            $this->uuidGenerator->generateUuid(),
            $userRegistrationDto->lastName,
            $userRegistrationDto->firstName,
            $userRegistrationDto->email
        );

        $userObjectName = 'AppBundle\Models\\'.$userRegistrationDto->userObjectType;
        $userDynamicObject = new $userObjectName(
            $this->uuidGenerator->generateUuid(),
            $identity,
            $password['encodedPassword'],
            $password['salt'],
            $userRegistrationDto->role
        );

        if ($userDynamicObject instanceof Juror && $userRegistrationDto->campaign) {
            $userDynamicObject->addCampaign($userRegistrationDto->campaign);
        }

        $this->entityManager->persist($userDynamicObject);
        $this->entityManager->flush();

        return $userDynamicObject;
    }

    /**
     * @param string $role
     *
     * @param string $type
     *
     * @return string
     * @throws \Exception
     */
    public function determineDataFromRole(string $role, string $type) : string
    {
        if ($type === 'object') {
            switch ($role) {
                case 'ROLE_ADMIN':
                    $objectType = 'Administrator';
                    break;
                case 'ROLE_CAMPAIGN_ADMIN':
                    $objectType = 'CampaignAdministrator';
                    break;
                case 'ROLE_JUROR':
                    $objectType = 'Juror';
                    break;
                default:
                    throw new \Exception('The role value you provide isn\'t correct. '.$role.' given.');
            }
        } elseif ($type === 'provider') {
            switch ($role) {
                case 'ROLE_ADMIN':
                    $objectType = 'administrator';
                    break;
                case 'ROLE_CAMPAIGN_ADMIN':
                    $objectType = 'campaign_admin';
                    break;
                case 'ROLE_JUROR':
                    $objectType = 'juror';
                    break;
                default:
                    throw new \Exception('The role value you provide isn\'t correct. '.$role.' given.');
            }
        } else {
            throw new \Exception('The type of the method determineDataFromRole isn\'t correct');
        }

        return $objectType;
    }

    /**
     * @param string $newEmail
     * @param string $oldEmail
     *
     * @throws \Exception
     * @internal param string $email
     *
     */
    public function verifyEmail(string $newEmail, string $oldEmail = null)
    {
        if (null !== $oldEmail && $newEmail === $oldEmail) {
            return;
        }

        $userRepositories = [
            $this->administratorRepository,
            $this->campaignAdministratorRepository,
            $this->jurorRepository,
        ];

        foreach ($userRepositories as $repository) {
            try {
                $user = $repository->loadUserByUsername($newEmail);

                if ($user) {
                    throw new \Exception('There is already one user with the email '.$newEmail);
                }
            } catch (UsernameNotFoundException $e) {
                continue;
            }
        }

        return;
    }

    /**
     * @param UserInterface $user
     *
     * @throws \Exception
     */
    public function refreshUser(UserInterface $user)
    {
        $provider = $this->determineProviderForUser($user);
        $provider->refreshUser($user);
    }

    /**
     * @param $plainPassword
     *
     * @return array
     */
    public function encodePasswordFromPlain($plainPassword)
    {
        $salt = $this->uuidGenerator->generateUuid();
        $encodedPassword = $this->passwordEncoder->encodePassword($plainPassword, $salt);

        return [
            'salt' => $salt,
            'encodedPassword' => $encodedPassword,
        ];
    }

    /**
     * @param UserInterface $user
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function authenticateUser(UserInterface $user)
    {
        $provider = $this->determineDataFromRole($user->getRoles()[0], 'provider');

        $token = new UsernamePasswordToken(
            $user,
            null,
            $provider,
            $user->getRoles()
        );

        $this->tokenStorage->setToken($token);
        $this->session->set('_security_'.$provider, serialize($token));
    }

    /**
     * @param UserInterface $user
     *
     * @return mixed
     * @throws \Exception
     */
    public function determineProviderForUser(UserInterface $user)
    {
        $userRepositories = [
            $this->administratorRepository,
            $this->campaignAdministratorRepository,
            $this->jurorRepository,
        ];

        foreach ($userRepositories as $repository) {
            $user = $repository->loadUserByUsername($user->getIdentity()->getEmail());

            if ($user) {
                return $repository;
            }
        }

        throw new \Exception("Sorry, there is no user provider found for this user.");
    }
}
