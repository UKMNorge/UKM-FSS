<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OidcAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        // Define the route or condition to check if this authenticator applies
        return $request->attributes->get('_route') === 'app_oidc_callback';
    }

    public function authenticate(Request $request): Passport
    {
        // Retrieve user data from session or request (e.g., session storage after OIDC flow)
        $userData = $request->getSession()->get('oidc_user'); 

        if (!$userData) {
            throw new AuthenticationException('OIDC user data not found.');
        }

        // Create and return a Passport object
        return new SelfValidatingPassport(
            new UserBadge($userData['sub'], function ($identifier) use ($userData): UserInterface {
                // Return an instance of your user (UserInterface implementation)
                return new OidcUser($userData); // A custom user class implementing UserInterface
            }),
            [new RememberMeBadge()]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Handle successful authentication
        return null; // Continue the request flow
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Handle authentication failure
        return new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }
}
