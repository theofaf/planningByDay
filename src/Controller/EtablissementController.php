<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\EtablissementService;
use App\Service\LogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Etablissement;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class EtablissementController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
        private readonly EtablissementService $etablissementService,
        private readonly LogService $logService,
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/etablissements",
     *     tags={"Etablissements"},
     *     summary="Créer un nouvel établissement",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Données de l'établissement à créer",
     *          @OA\JsonContent(ref=@Model(type=Etablissement::class, groups={"nelmio"}))
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Établissement créé avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Etablissement::class, groups={"etablissement"}))
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
     * @Rest\Post("/api/etablissements")
     * @Security(name="Bearer")
     */
    public function postEtablissement(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$this->etablissementService->isDataValide($data)) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $etablissement = (new Etablissement())
                ->setLibelle($data['libelle'])
                ->setNumVoie((int)$data['numVoie'])
                ->setRue($data['rue'])
                ->setVille($data['ville'])
                ->setCodePostal($data['codePostal'])
                ->setNumeroTel($data['numeroTel'])
                ->setStatutAbonnement(false)
            ;

            $this->em->persist($etablissement);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La création de l'établissement a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $etablissementSerealize = $this->serializer->serialize($etablissement, 'json', ['groups' => 'etablissement']);
        return new JsonResponse(['message' => 'Établissement créé avec succès', 'etablissement' => $etablissementSerealize], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/etablissements/{etablissementId}",
     *     tags={"Etablissements"},
     *     summary="Mettre à jour les détails d'un établissement par ID",
     *     @OA\Parameter(
     *         name="etablissementId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de l'établissement"
     *     ),
     *     @OA\RequestBody(
     *           required=true,
     *           description="Données de l'établissement à modifier",
     *           @OA\JsonContent(ref=@Model(type=Etablissement::class, groups={"nelmio"}))
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Établissement mis à jour avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Etablissement::class, groups={"etablissement"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Établissement non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Put("/api/etablissements/{etablissementId}")
     * @Security(name="Bearer")
     */
    public function putEtablissement(int $etablissementId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            if (!$this->etablissementService->isDataValide($data)) {
                return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
            }

            $etablissement = $this->em->getRepository(Etablissement::class)->find($etablissementId);

            if (!$etablissement) {
                return new JsonResponse(['message' => 'Établissement non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $etablissement
                ->setLibelle($data['libelle'])
                ->setNumVoie($data['numVoie'])
                ->setRue($data['rue'])
                ->setVille($data['ville'])
                ->setCodePostal($data['codePostal'])
                ->setNumeroTel($data['numeroTel'])
            ;
        } catch (Exception $exception) {
            $this->logService->insererLog("La modification de l'établissement [$etablissementId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->em->flush();

        $etablissementSerealize = $this->serializer->serialize($etablissement, 'json', ['groups' => 'etablissement']);
        return new JsonResponse(['message' => 'Établissement mis à jour avec succès', 'etablissement' => $etablissementSerealize], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/etablissements/{etablissementId}",
     *     tags={"Etablissements"},
     *     summary="Supprimer un établissement par ID",
     *     @OA\Parameter(
     *         name="etablissementId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de l'établissement à supprimer"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Établissement supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Établissement non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     * @Rest\Delete("/api/etablissements/{etablissementId}")
     * @Security(name="Bearer")
     */
    public function deleteEtablissement(int $etablissementId): JsonResponse
    {
        try {
            $etablissement = $this->em->getRepository(Etablissement::class)->find($etablissementId);
            $etablissementSansAffection = $this->em->getRepository(Etablissement::class)->findOneBy(['libelle' => Etablissement::REFERENCE_SANS_AFFECTION]);

            if (!$etablissement || !$etablissementSansAffection || ($etablissementSansAffection->getId() === $etablissementId)) {
                return new JsonResponse(['message' => 'Établissement non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $utilisateurs = $this->em->getRepository(Utilisateur::class)->findBy(['etablissement' => $etablissement->getId()]);
            foreach ($utilisateurs as $utilisateur) {
                $utilisateur->setEtablissement($etablissementSansAffection);
            }
            $this->em->remove($etablissement);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La suppression de l'établissement [$etablissementId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Établissement supprimé avec succès'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *     path="/api/etablissements/{etablissementId}",
     *     tags={"Etablissements"},
     *     summary="Récupère un établissement par son ID",
     *     @OA\Parameter(
     *          name="etablissementId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de l'établissement"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="L'établissement est retourné",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Etablissement::class, groups={"etablissement"}))
     *           )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="L'établissement n'a pas été trouvé"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/etablissements/{etablissementId}")
     * @Security(name="Bearer")
     */
    public function getEtablissementParId(int $etablissementId): JsonResponse
    {
        try {
            $etablissement = $this->em->getRepository(Etablissement::class)->find($etablissementId);

            if (!$etablissement) {
                return new JsonResponse(['message' => 'Établissement non trouvé'], Response::HTTP_NOT_FOUND);
            }
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération de l'établissement [$etablissementId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($etablissement, 'json', ['groups' => 'etablissement']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/etablissements",
     *     tags={"Etablissements"},
     *     summary="Récupère tous les établissements",
     *     @OA\Response(
     *          response=200,
     *          description="Tous les établissements sont retournés",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Etablissement::class, groups={"etablissement"}))
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/etablissements")
     * @Security(name="Bearer")
     */
    public function getEtablissements(): JsonResponse
    {
        try {
            $etablissement = $this->em->getRepository(Etablissement::class)->findAll();
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des établissements a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($etablissement, 'json', ['groups' => 'etablissement']), Response::HTTP_OK);
    }
}
