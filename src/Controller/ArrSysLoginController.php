<?php

namespace App\Controller;

use App\Service\ArrSysOpenIDConnect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Exception;


class ArrSysLoginController extends AbstractController
{
    private $openIDConnect;

    public function __construct(ArrSysOpenIDConnect $openIDConnect)
    {
        $this->openIDConnect = $openIDConnect;
    }

    #[Route('/login/login', name: 'api_login', methods: ['GET'])]
    public function login() {
        // Redirect the user to the Vipps login page
        try{
            echo '<h3>calling login/login</h3>';
            $this->openIDConnect->authenticate();
        } catch(Exception $e) {
            return $this->redirectToRoute('ukm_delta_ukmid_homepage');
        }
    }

    #[Route('/login/callback', name: 'api_callback', methods: ['GET'])]
    public function callback(Request $request): Response {
        try{
            $this->openIDConnect->authenticate();
        } catch(Exception $e) {
            var_dump($e->getMessage());
            return $this->redirectToRoute('/');
            exit;
        }

        $token = null;
        $userInfo = null;

        try{
            $token = $this->openIDConnect->getIdToken();            
            // Retrieve user information after login
        } catch(Exception $e) {
            return $this->redirectToRoute('/');
        }

        // Check if the JWT signature to ensure its authenticity
        if ($this->openIDConnect->verifyJWTsignature($token) != true) {
            return $this->redirectToRoute('/');
        }
        
        try{
            $userInfo = $this->openIDConnect->getUserInfo();        
            // Retrieve user information after login
        } catch(Exception $e) {
            return $this->redirectToRoute('/');
        }
        
        // Check if the token or user information is null
        if($token == null || $userInfo == null) {
            return $this->redirectToRoute('/');
        }

        // Check if the user is logged in by checking if the username is set
        if($userInfo->username == null) {
            return $this->redirectToRoute('/');
        }

        // The user is logged in

        die;
    }

}
