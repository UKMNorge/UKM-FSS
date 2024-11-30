<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class OidcUser implements UserInterface
{
    private array $userData;

    public function __construct(array $userData)
    {
        $this->userData = $userData;
    }

    public function getUserIdentifier(): string
    {
        return $this->userData['username'];
    }

    public function getRoles(): array
    {
        return $this->userData['roles'] ?? ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // No sensitive data to clear
    }
}
