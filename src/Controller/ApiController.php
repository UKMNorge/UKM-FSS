<?php

namespace App\Controller;

use App\Service\ArrSysOpenIDConnect;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController
{
    private ArrSysOpenIDConnect $openIDConnect;

    public function __construct(ArrSysOpenIDConnect $openIDConnect)
    {
        $this->openIDConnect = $openIDConnect;
    }

    #[Route('/api/hello', name: 'api_hello', methods: ['GET'])]
    public function hello(Request $request): JsonResponse
    {
        // Extract the Authorization header
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        // Extract the token from the header
        $accessToken = substr($authHeader, 7); // Remove 'Bearer ' prefix

        try {
            // Verify the token with OpenID Connect
            $this->openIDConnect->setAccessToken($accessToken);
            $userInfo = $this->openIDConnect->getUserInfo();

            return new JsonResponse([
                'message' => 'Hello, ' . $userInfo->name,
                'user_info' => $userInfo
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid token'], 401);
        }
    }
}