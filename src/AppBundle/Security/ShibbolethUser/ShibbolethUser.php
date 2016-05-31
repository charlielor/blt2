<?php

namespace AppBundle\Security\ShibbolethUser;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class ShibbolethUser implements UserInterface, EquatableInterface
{
    private $username;
    private $roles;

    public function __construct($username, array $roles)
    {
        $this->username = $username;
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof ShibbolethUser) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}