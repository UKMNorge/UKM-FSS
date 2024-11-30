<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class OidcUserProvider implements UserProviderInterface
{
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Create a new OidcUser using the identifier
        return new OidcUser([
            'sub' => $identifier, // Use OIDC sub as unique identifier
            'username' => $identifier,
            'roles' => ['ROLE_USER'], // Assign default roles
        ]);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported class "%s".', get_class($user)));
        }

        // Stateless systems do not need to refresh the user
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        // Indicate that this provider supports OidcUser
        return $class === OidcUser::class;
    }
}
