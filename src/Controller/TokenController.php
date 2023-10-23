<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\SerializerInterface;

class TokenController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface  $serializer,
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/validate/token",
     *     tags={"Authentification"},
     *     summary="Valider un token d'authentification Bearer",
     *     @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          description="Le token d'authentification Bearer",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Token valide",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="valid", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token Bearer non fourni ou invalide"
     *     )
     * )
     *
     * @Rest\Post("/api/validate/token")
     * @Security(name="Bearer")
     */
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

        return new JsonResponse([
            'valid' => true,
            'user' => $this->serializer->serialize($this->getUser(), 'json', ['groups' => 'utilisateur']),
        ]);
    }
}