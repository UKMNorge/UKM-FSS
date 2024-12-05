<?php

namespace App\Service;

use Exception;
use Jumbojett\OpenIDConnectClient;

class ArrSysOpenIDConnect
{
    private $oidc;

    public function __construct() {
        $redirectURL = $_ENV['OPEN_ID_REDIRECT_URL'];
        $url = $_ENV['OPEN_ID_URL'];


        $this->oidc = new OpenIDConnectClient(
            $url,
            $_ENV['FSS_OPENID_CLIENT_ID'],  // client ID
            $_ENV['FSS_OPENID_CLIENT_SECRET']   // client secret
        );        

        $this->oidc->setIssuer($url);
        $this->oidc->setRedirectURL($redirectURL);
        $this->oidc->addScope(['openid', 'profile', 'email', 'phone']);

    }

    public function authenticate() {
        try { 
            // Enforce client_secret_post method
            $this->oidc->setTokenEndpointAuthMethodsSupported(['client_secret_post']);
    
            return $this->oidc->authenticate();
        } catch (Exception $e) {
            throw new Exception("Failed to authenticate with WPAUTH: " . $e->getMessage());
        }
    }

    public function getUserInfo() {
        try {
            return $this->oidc->requestUserInfo();
        } catch(Exception $e) {
            throw new Exception("Failed to get user info from WPAUTH: " . $e->getMessage());
        }
    }

    public function getIdToken() {
        return $this->oidc->getIdToken();
    }

    public function verifyJWTsignature($idToken) : bool {
        return $this->oidc->verifyJWTsignature($idToken);
    }
}
