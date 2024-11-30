<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OidcController extends AbstractController
{
    #[Route('/oidc/callback', name: 'app_oidc_callback')]
    public function callback(Request $request): Response
    {
        // Fetch user info from OIDC provider (this assumes you already implemented the OIDC flow)
        $oidcUser = [
            'sub' => 'unique-id-123', // OIDC subject
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'roles' => ['ROLE_USER'], // Optional
        ];

        // Store in session
        $request->getSession()->set('oidc_user', $oidcUser);

        // Redirect to a secure page (the authenticator will now log in the user)
        return $this->redirectToRoute('secure_page');
    }
}