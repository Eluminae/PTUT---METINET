<?php

namespace AppBundle\Models;

use AppBundle\Models\Campaign;
use AppBundle\Models\Identity;
use AppBundle\Models\Password;
use Symfony\Component\Security\Core\User\UserInterface;

class Juror implements UserInterface
{
    private $id;
    private $identity;
    private $password;
    private $campaign;

    public function __construct(string $id, Identity $identity, Password $password, Campaign $campaign)
    {
        $this->id = $id;
        $this->identity = $identity;
        $this->password = $password;
        $this->campaign = $campaign;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
