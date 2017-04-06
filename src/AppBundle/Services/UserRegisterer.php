<?php
/**
 * Created by PhpStorm.
 * User: corentinbouix
 * Date: 03/04/2017
 * Time: 15:46
 */

namespace AppBundle\Services;


use AppBundle\Dtos\UserRegistration;
use AppBundle\Models\Identity;
use AppBundle\Models\Juror;
use AppBundle\Services\UuidGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRegisterer
{
    private $passwordEncoder;
    private $uuidGenerator;
    private $entityManager;

    /**
     * UserRegisterer constructor.
     *
     * @param PasswordEncoderInterface $passwordEncoder
     * @param UuidGenerator            $uuidGenerator
     * @param EntityManagerInterface   $entityManager
     */
    public function __construct(
        PasswordEncoderInterface $passwordEncoder,
        UuidGenerator $uuidGenerator,
        EntityManagerInterface $entityManager
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->uuidGenerator = $uuidGenerator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param UserRegistration $userRegistrationDto
     *
     * @return UserInterface
     */
    public function signUp(UserRegistration $userRegistrationDto)
    {
        $salt = $this->uuidGenerator->generateUuid();
        $encodedPassword = $this->passwordEncoder->encodePassword($userRegistrationDto->password, $salt);

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
            $encodedPassword,
            $salt,
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
                    throw new \Exception('The role value you provide isn\'t correct.');
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
                    throw new \Exception('The role value you provide isn\'t correct.');
            }
        } else {
            throw new \Exception('The type of the method determineDataFromRole isn\'t correct');
        }

        return $objectType;
    }
}