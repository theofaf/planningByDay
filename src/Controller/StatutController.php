<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use App\Entity\Statut;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Symfony\Component\Serializer\SerializerInterface;

class StatutController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/statuts",
     *     tags={"Statuts"},
     *     summary="Récupère les statuts",
     *     @OA\Response(
     *          response=200,
     *          description="Les statuts sont retournés",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Statut::class, groups={"statut"}))
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/statuts")
     * @Security(name="Bearer")
     */
    public function getStatuts(): JsonResponse
    {
        try {
            $statuts = $this->em->getRepository(Statut::class)->findAll();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($statuts, Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/statuts/{statutId}",
     *     tags={"Statuts"},
     *     summary="Récupère un statut par ID",
     *     @OA\Parameter(
     *          name="statutId",
     *          @OA\Schema(type="string"),
     *          in="path",
     *          required=true,
     *          description="ID du statut"
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Le statut est retourné",
     *          @OA\JsonContent(
     *               type="object",
     *               @OA\Property(property="id", type="integer"),
     *               @OA\Property(property="libelle", type="string"),
     *               @OA\Property(property="libelle_technique", type="string")
     *           )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/statuts/{statutId}")
     * @Security(name="Bearer")
     */
    public function getStatutParId(int $statutId): JsonResponse
    {
        try {
            $statut = $this->em->getRepository(Statut::class)->find($statutId);
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($statut, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/statuts",
     *     tags={"Statuts"},
     *     summary="Créer un nouveau statut",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du statut à créer",
     *         @OA\JsonContent(ref=@Model(type=Statut::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Statut créé avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Statut::class, groups={"statut"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Post("/api/statuts")
     * @Security(name="Bearer")
     */
    public function postStatut(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            null === $data
            || $data['libelle'] === null
            || $data['libelle_technique'] === null
        ) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        $statut = (new Statut())
            ->setLibelle($data['libelle'])
            ->setLibelleTechnique($data['libelle_technique'])
        ;

        try {
            $this->em->persist($statut);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $statutSerialize = $this->serializer->serialize($statut, 'json', ['groups' => 'statut']);

        return new JsonResponse([
            'message' => 'Statut créé avec succès',
            'statut' => $statutSerialize,
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/statuts/{statutId}",
     *     tags={"Statuts"},
     *     summary="Mettre à jour les détails d'un statut par ID",
     *     @OA\Parameter(
     *         name="statutId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID du statut"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du statut à mettre à jour",
     *         @OA\JsonContent(ref=@Model(type=Statut::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statut mis à jour avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Statut::class, groups={"statut"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Statut non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Put("/api/statuts/{statutId}")
     * @Security(name="Bearer")
     */
    public function putStatut(int $statutId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            null === $data
            || $data['libelle'] === null
            || $data['libelle_technique'] === null
        ) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        $statut = $this->em->getRepository(Statut::class)->find($statutId);

        if (!$statut) {
            return new JsonResponse(['message' => 'Statut non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $statut
            ->setLibelle($data['libelle'])
            ->setLibelleTechnique($data['libelle_technique'])
        ;

        try {
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $statutSerialize = $this->serializer->serialize($statut, 'json', ['groups' => 'statut']);

        return new JsonResponse([
            'message' => 'Statut mis à jour avec succès',
            'statut' => $statutSerialize,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/statuts/{statutId}",
     *     tags={"Statuts"},
     *     summary="Supprimer un statut par ID",
     *     @OA\Parameter(
     *         name="statutId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID du statut à supprimer"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Statut supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Statuts non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     * @Rest\Delete("/api/statuts/{statutId}")
     * @Security(name="Bearer")
     */
    public function deleteStatut(int $statutId): JsonResponse
    {
        $statut = $this->em->getRepository(Statut::class)->find($statutId);

        if (!$statut) {
            return new JsonResponse(['message' => 'Statut non trouvé'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->em->remove($statut);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Statut supprimé avec succès'], Response::HTTP_NO_CONTENT);
    }
}
