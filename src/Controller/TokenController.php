<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TokenController extends AbstractController
{
    #[Route('/api/token/validate', name: 'validate_token', methods: "POST")]
    public function validateToken(Request $request): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $token = null;

        if ($authorizationHeader && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            $token = $matches[1];
        }

        if (!$token) {
            throw new AuthenticationException('Bearer Token not provided.');
        }

        return new JsonResponse(['valid' => true]);
    }
}