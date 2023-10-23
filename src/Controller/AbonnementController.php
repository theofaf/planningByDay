<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Etablissement;
use App\Service\LogService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class AbonnementController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
        private readonly LogService $logService,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/abonnements",
     *     tags={"Abonnements"},
     *     summary="Récupère les abonnements",
     *     @OA\Response(
     *          response=200,
     *          description="Les abonnements sont retournés",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Abonnement::class, groups={"nelmio"}))
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/abonnements")
     * @Security(name="Bearer")
     */
    public function getAbonnements(): JsonResponse
    {
        try {
            $abonnements = $this->em->getRepository(Abonnement::class)->findAll();
        } catch (Exception $e) {
            $this->logService->insererLog("La récupération des abonnements a échoué.", $e);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($abonnements, Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/abonnements/{abonnementId}",
     *     tags={"Abonnements"},
     *     summary="Récupère un abonnement par ID",
     *     @OA\Parameter(
     *          name="abonnementId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de l'abonnement"
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="L'abonnement est retourné",
     *          @OA\JsonContent(
     *               type="object",
     *               @OA\Property(property="id", type="integer"),
     *               @OA\Property(property="libelle", type="string"),
     *               @OA\Property(property="libelle_technique", type="string"),
     *               @OA\Property(property="prix", type="number")
     *           )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/abonnements/{abonnementId}")
     * @Security(name="Bearer")
     */
    public function getAbonnementParId(int $abonnementId): JsonResponse
    {
       try {
            $abonnement = $this->em->getRepository(Abonnement::class)->find($abonnementId);
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération de l'abonnement avec l'id [$abonnementId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($abonnement, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/abonnements",
     *     tags={"Abonnements"},
     *     summary="Créer un nouvel abonnement",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l'abonnement à créer",
     *         @OA\JsonContent(ref=@Model(type=Abonnement::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Abonnement créé avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Abonnement::class, groups={"nelmio"}))
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
     * @Rest\Post("/api/abonnements")
     * @Security(name="Bearer")
     */
    public function postAbonnement(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            null === $data
            || !isset($data['libelle'])
            || !isset($data['libelle_technique'])
            || !isset($data['prix'])
        ) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        $abonnement = (new Abonnement())
            ->setLibelle($data['libelle'])
            ->setLibelleTechnique($data['libelle_technique'])
            ->setPrix($data['prix'])
        ;

        try {
            $this->em->persist($abonnement);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La création de l'abonnement a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $abonnementSerialize = $this->serializer->serialize($abonnement, 'json', ['groups' => 'abonnement']);
        return new JsonResponse(['message' => 'Abonnement créé avec succès' , 'abonnement' => $abonnementSerialize], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/abonnements/{abonnementId}",
     *     tags={"Abonnements"},
     *     summary="Mettre à jour les détails d'un abonnement par ID",
     *     @OA\Parameter(
     *         name="abonnementId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de l'abonnement"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l'abonnement à mettre à jour",
     *         @OA\JsonContent(ref=@Model(type=Abonnement::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonnement mis à jour avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Abonnement::class, groups={"nelmio"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Abonnement non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Put("/api/abonnements/{abonnementId}")
     * @Security(name="Bearer")
     */
    public function putAbonnement(int $abonnementId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            null === $data
            || !isset($data['libelle'])
            || !isset($data['libelle_technique'])
            || !isset($data['prix'])
        ) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $abonnement = $this->em->getRepository(Abonnement::class)->find($abonnementId);
            if (!$abonnement) {
                return new JsonResponse(['message' => 'Abonnement non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $abonnement
                ->setLibelle($data['libelle'])
                ->setLibelleTechnique($data['libelle_technique'])
                ->setPrix($data['prix'])
            ;

            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La modification de l'abonnement a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $abonnementSerialize = $this->serializer->serialize($abonnement, 'json', ['groups' => 'abonnement']);
        return new JsonResponse(['message' => 'Abonnement mis à jour avec succès', 'abonnement' => $abonnementSerialize], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/abonnements/{abonnementId}",
     *     tags={"Abonnements"},
     *     summary="Supprimer un abonnement par ID",
     *     @OA\Parameter(
     *         name="abonnementId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de l'abonnement à supprimer"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Abonnement supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Abonnement non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     * @Rest\Delete("/api/abonnements/{abonnementId}")
     * @Security(name="Bearer")
     */
    public function deleteAbonnement(int $abonnementId): JsonResponse
    {
        $abonnement = $this->em->getRepository(Abonnement::class)->find($abonnementId);

        if (!$abonnement) {
            return new JsonResponse(['message' => 'Abonnement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->em->remove($abonnement);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La suppression de l'abonnement a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Abonnement supprimé avec succès'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Patch(
     *     path="/api/abonnements/subscribe/{etablissementId}/{abonnementId}",
     *     tags={"Abonnements"},
     *     summary="Souscrire un abonnement pour un établissement",
     *     @OA\Parameter(
     *         name="etablissementId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de l'établissement"
     *     ),
     *     @OA\Parameter(
     *         name="abonnementId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de l'abonnement"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonnement souscrit avec succès"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Établissement déjà abonné ou données invalides"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Établissement ou abonnement non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Patch("/api/abonnements/subscribe/{etablissementId}/{abonnementId}")
     * @Security(name="Bearer")
     */
    public function subscribeAbonnement(int $etablissementId, int $abonnementId): JsonResponse
    {
        $abonnement = $this->em->getRepository(Abonnement::class)->find($abonnementId);
        $etablissement = $this->em->getRepository(Etablissement::class)->find($etablissementId);

        if (!$abonnement) {
            return new JsonResponse(['message' => 'Abonnement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if (!$etablissement) {
            return new JsonResponse(['message' => 'Etablissement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if ($etablissement->getAbonnement() !== null) {
            return new JsonResponse(['message' => 'L\'établissement a déjà souscrit à un abonnement'], Response::HTTP_BAD_REQUEST);
        }

        $etablissement
            ->setAbonnement($abonnement)
            ->setDateAbonnement(new DateTime())
            ->setStatutAbonnement(true)
        ;

        try {
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("L'inscription à l'abonnement [$abonnementId] pour l'établissement [$etablissementId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Abonnement souscrit avec succès'], Response::HTTP_OK);
    }

    /**
     * @OA\Patch(
     *     path="/api/abonnements/cancel/{etablissementId}/{abonnementId}",
     *     tags={"Abonnements"},
     *     summary="Annuler un abonnement pour un établissement",
     *     @OA\Parameter(
     *         name="etablissementId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de l'établissement"
     *     ),
     *     @OA\Parameter(
     *         name="abonnementId",
     *         in="path",
     *         required=true,
     *         description="ID de l'abonnement"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonnement annulé avec succès"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="L'établissement ne possède pas d'abonnement"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Établissement ou abonnement non trouvé"
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     * @Rest\Patch("/api/abonnements/cancel/{etablissementId}/{abonnementId}")
     * @Security(name="Bearer")
     */
    public function cancelAbonnement(int $etablissementId, int $abonnementId): JsonResponse
    {
        $abonnement = $this->em->getRepository(Abonnement::class)->find($abonnementId);
        $etablissement = $this->em->getRepository(Etablissement::class)->find($etablissementId);

        if (!$abonnement) {
            return new JsonResponse(['message' => 'Abonnement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if (!$etablissement) {
            return new JsonResponse(['message' => 'Etablissement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if ($etablissement->getAbonnement() === null) {
            return new JsonResponse(['message' => 'L\'établissement ne possède aucun abonnement'], Response::HTTP_BAD_REQUEST);
        }

        $etablissement
            ->setAbonnement(null)
            ->setDateAbonnement(null)
            ->setStatutAbonnement(false)
        ;

        try {
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La désinscription à l'abonnement [$abonnementId] pour l'établissement [$etablissementId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Abonnement annulé avec succès'], Response::HTTP_OK);
    }
}
