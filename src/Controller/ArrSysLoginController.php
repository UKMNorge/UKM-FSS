<?php

namespace App\Controller;

use App\Security\OidcUser; // Import your custom OidcUser class
use App\Service\ArrSysOpenIDConnect;
use App\Security\OidcAuthenticator; // Import your custom authenticator
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Exception;

class ArrSysLoginController extends AbstractController {
    private $openIDConnect;

    public function __construct(ArrSysOpenIDConnect $openIDConnect)
    {
        $this->openIDConnect = $openIDConnect;
    }

    #[Route('/login/login', name: 'api_login', methods: ['GET'])]
    public function login() {
        // Redirect the user to the Vipps login page
        try {
            $this->openIDConnect->authenticate();
        } catch(Exception $e) {
            return $this->redirectToRoute('/');
        }
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/login/callback', name: 'api_callback', methods: ['GET'])]
    public function callback(
        Request $request,
        UserAuthenticatorInterface $userAuthenticator,
        OidcAuthenticator $authenticator
    ): Response {
        try {
            $this->openIDConnect->authenticate();
        } catch (\Exception $e) {
            return $this->redirectToRoute('/');
        }

        $token = $this->openIDConnect->getIdToken();
        $userInfo = $this->openIDConnect->getUserInfo();

        if (!$token || !$userInfo || empty($userInfo->username) || $this->openIDConnect->verifyJWTsignature($token) != true) {
            return $this->redirectToRoute('/');
        }

        
        // Create a user object
        $user = new OidcUser([
            'sub' => $userInfo->sub,
            'username' => $userInfo->username,
            'roles' => ['ROLE_USER'], // Adjust roles as necessary
        ]);

        // Authenticate the user and start a session
        $userAuthenticator->authenticateUser(
            $user,
            $authenticator, // Pass the OidcAuthenticator instance
            $request // Pass the Request object
        );

        // Redirect the user to their dashboard or another route
        return $this->redirectToRoute('home'); // Use the route name for /home

    }
}
