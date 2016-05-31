<?php

namespace AppBundle\Security\ShibbolethUser;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use AppBundle\Security\ShibbolethUser\ShibbolethUser;

class ShibbolethUserProvider implements UserProviderInterface {

    public function loadUserByUsername($username)
    {
        return new ShibbolethUser(
            $username,
            array('ROLE_USER')
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof ShibbolethUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $user;
    }

    public function supportsClass($class)
    {
        return 'AppBundle\Security\ShibbolethUser\ShibbolethUser' === $class;
    }
}