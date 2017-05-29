<?php

namespace AppBundle\Repositories;

use AppBundle\Models\Administrator;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OrmAdministratorRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        $query = $this->createQueryBuilder('a')
            ->join('a.identity', 'i')
            ->where('i.email = :email')
            ->setParameter('email', $username)
            ->setMaxResults(1)
            ->getQuery()
        ;

        $user = $query->getResult();

        if (count($user) < 1) {
            throw new UsernameNotFoundException();
        }

        return $user[0];
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {

            throw new UnsupportedUserException();
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function supportsClass($class)
    {
        return (Administrator::class === $class);
    }
}
