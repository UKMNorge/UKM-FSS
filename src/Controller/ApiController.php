<?php

namespace App\Controller;

use App\Service\ArrSysOpenIDConnect;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ApiController extends AbstractController {
    private ArrSysOpenIDConnect $openIDConnect;

    public function __construct(ArrSysOpenIDConnect $openIDConnect)
    {
        $this->openIDConnect = $openIDConnect;
    }

    #[Route('/api/hello', name: 'api_hello', methods: ['GET'])]
    public function hello(Request $request) {   
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        
        return new JsonResponse(['status' => 'Login OK, can continue...'], 200);
    }



    #[Route('/home', name: 'home', methods: ['GET'])]
    public function home(Request $request): Response {

        $user = $this->getUser();
        $name = $user ? $user->getUserIdentifier() : 'NO USER';

        // Render the HTML response
        return new Response(
            '<!DOCTYPE html>
            <html>
                <head>
                    <title>Welcome</title>
                </head>
                <body>
                    <h1>Welcome to the Home Page</h1>
                    <p>User '. $name .'</p>

                    <a href="/login/login">Login</a><br>
                    <a href="/logout">Logout</a>
                </body>
            </html>',
            200,
            ['Content-Type' => 'text/html']
        );
    }

}