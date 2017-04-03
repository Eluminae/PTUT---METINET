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
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserRegisterer
{
    private $passwordEncoder;
    private $uuidGenerator;

    /**
     * UserRegisterer constructor.
     *
     * @param PasswordEncoderInterface $passwordEncoder
     * @param UuidGenerator            $uuidGenerator
     */
    public function __construct(PasswordEncoderInterface $passwordEncoder, UuidGenerator $uuidGenerator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function signUp(UserRegistration $userRegistrationDto)
    {
        $salt = $this->uuidGenerator->generateUuid();
        $this->passwordEncoder->encodePassword($userRegistrationDto->password, $salt);

        $identity = new Identity(
            $this->uuidGenerator->generateUuid(),
            $userRegistrationDto->lastName,
            $userRegistrationDto->firstName,
            $userRegistrationDto->email
        );

        $userObjectName = 'AppBundle\Models\\'.$userRegistrationDto->userObjectType();
        $userDynamicObject = new $userObjectName(
            $this->uuidGenerator->generateUuid(),
            $identity,
            $userRegistrationDto->password
        );

        if ($userDynamicObject instanceof Juror && !$userRegistrationDto->campains->isEmpty()) {
            $userDynamicObject->addCampaign($userRegistrationDto->campain);
        }

        // todo
        // todo Handle juror campaign



        // Passer le role ?????? -> attribute ?
    }


    // Construct an object from the DTO of User
    // Handle it an create identity -> verify data consistency
}