<?php

namespace App\Service;

use Exception;
use Jumbojett\OpenIDConnectClient;

class ArrSysOpenIDConnect
{
    private $oidc;

    public function __construct() {
        $redirectURL = "https://liker.ukm.dev/login/callback";
        $vippsURL = 'https://ukm.no/';
        
        $this->oidc = new OpenIDConnectClient(
            $vippsURL,
            'client_id_random_string',  // client ID
            'secretTestDev'   // client secret
        );        

        $this->oidc->setIssuer($vippsURL);
        $this->oidc->setRedirectURL($redirectURL);
    }

    public function authenticate() {
        try {
            // Set client credentials
            $this->oidc->setClientID('client_id_random_string');
            $this->oidc->setClientSecret('secretTestDev');
    
            // Add scopes
            $this->oidc->addScope(['openid', 'profile']);
    
            // Enforce client_secret_post method
            $this->oidc->setTokenEndpointAuthMethodsSupported(['client_secret_post']);
    
            return $this->oidc->authenticate();
        } catch (Exception $e) {
            throw new Exception("Failed to authenticate with WPAUTH: " . $e->getMessage());
        }
    }

    public function getUserInfo() {
        return $this->oidc->requestUserInfo();
    }

    public function getIdToken() {
        return $this->oidc->getIdToken();
    }

    public function verifyJWTsignature($idToken) : bool {
        return $this->oidc->verifyJWTsignature($idToken);
    }
}
