<?php

namespace App\Controller;


use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;

class TokenController extends AbstractController
{
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
     *             @OA\Property(property="valid", type="boolean", example=true)
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
            return new JsonResponse('token invalid', Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(['valid' => true]);
    }
}
