<?php

namespace App\Controller;

use App\Service\LogService;
use App\Service\UtilisateurService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Etablissement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;
use Symfony\Component\Serializer\SerializerInterface;

class UtilisateurController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UtilisateurService $utilisateurService,
        private readonly SerializerInterface $serializer,
        private readonly UserPasswordHasherInterface  $passwordHasher,
        private readonly LogService $logService,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/utilisateurs",
     *     tags={"Utilisateurs"},
     *     summary="Récupère tous les utilisateurs",
     *     @OA\Response(
     *          response=200,
     *          description="Tous les utilisateurs sont retournés",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Utilisateur::class, groups={"utilisateur"}))
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/utilisateurs")
     * @Security(name="Bearer")
     */
    public function getUtilisateurs(): JsonResponse
    {
        try {
            $utilisateurs = $this->em->getRepository(Utilisateur::class)->findAllExceptSupportRole();
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des utilisateurs a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($utilisateurs, 'json', ['groups' => 'utilisateur']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/utilisateurs/etablissement/{etablissementId}",
     *     tags={"Utilisateurs"},
     *     summary="Récupère les utilisateurs d'un établissementId",
     *     @OA\Parameter(
     *          name="etablissementId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de l'établissement"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="La liste des utilisateurs d'un établissement est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Etablissement::class, groups={"batiment"}))
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
     * @Rest\Get("/api/utilisateurs/etablissement/{etablissementId}")
     * @Security(name="Bearer")
     */
    public function getUtilisateursParEtablissementId(int $etablissementId): JsonResponse
    {
        try {
            $etablissement = $this->em->getRepository(Etablissement::class)->find($etablissementId);

            if (!$etablissement) {
                return new JsonResponse(['message' => "L'établissement n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $utilisateurs = $this->em->getRepository(Utilisateur::class)->findBy(['etablissement' => $etablissement->getId()]);
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des utilisateurs del'établissement [$etablissementId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($utilisateurs, 'json', ['groups' => 'utilisateur']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/utilisateurs/{utilisateurId}",
     *     tags={"Utilisateurs"},
     *     summary="Récupère un utilisateur par son ID",
     *     @OA\Parameter(
     *          name="utilisateurId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID du utilisateur"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="L'utilisateur est retourné",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Utilisateur::class, groups={"utilisateur"}))
     *           )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="L'utilisateur n'a pas été trouvé"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/utilisateurs/{utilisateurId}")
     * @Security(name="Bearer")
     */
    public function getUtilisateurParId(int $utilisateurId): JsonResponse
    {
        try {
            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);

            if (!$utilisateur) {
                return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
            }
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération de l'utilisateur [$utilisateurId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($utilisateur, 'json', ['groups' => 'utilisateur']), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/utilisateurs",
     *     tags={"Utilisateurs"},
     *     summary="Créer un nouvel utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l'utilisateur à créer",
     *         @OA\JsonContent(ref=@Model(type=Utilisateur::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Utilisateur::class, groups={"utilisateur"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Établissement non trouvé"
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Post("/api/utilisateurs")
     * @Security(name="Bearer")
     */
    public function postUtilisateur(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$this->utilisateurService->isDataValide($data, false)) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $utilisateur = (new Utilisateur());
            $utilisateur
                ->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setEmail($data['email'])
                ->setRoles([$data['roles']])
                ->setPassword($this->passwordHasher->hashPassword($utilisateur, 'Azerty123*'))
                ->setDateDerniereAction(new DateTime());

            $etablissement = $this->em->getRepository(Etablissement::class)->find($data['etablissementId']);
            if (!$etablissement) {
                return new JsonResponse(['message' => 'Établissement non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $utilisateur->setEtablissement($etablissement);
            $this->em->persist($utilisateur);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La création de l'utilisateur a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $utilisateurSerealize = $this->serializer->serialize($utilisateur, 'json', ['groups' => 'utilisateur']);
        return new JsonResponse(['message' => 'Utilisateur créé avec succès', 'utilisateur' => $utilisateurSerealize], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/utilisateurs/{utilisateurId}",
     *     tags={"Utilisateurs"},
     *     summary="Modifier un nouvel utilisateur",
     *     @OA\Parameter(
     *           name="utilisateurId",
     *           @OA\Schema(type="integer"),
     *           in="path",
     *           required=true,
     *           description="ID du utilisateur"
     *       ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l'utilisateur à modifier",
     *         @OA\JsonContent(ref=@Model(type=Utilisateur::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur modifié avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Utilisateur::class, groups={"utilisateur"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Établissement non trouvé"
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Put("/api/utilisateurs/{utilisateurId}")
     * @Security(name="Bearer")
     */
    public function putUtilisateur(int $utilisateurId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$this->utilisateurService->isDataValide($data)) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);
            $password = $data['password'];
            $passwordNew = $data['passwordNew'];

            if (!$utilisateur) {
                return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $isValid = $this->passwordHasher->isPasswordValid($utilisateur, $password);

            if (
                isset($password) && isset($passwordNew) && $isValid
            ) {
                $utilisateur->setPassword($this->passwordHasher->hashPassword($utilisateur, $passwordNew));
            }

            $utilisateur
                ->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setEmail($data['email'])
                ->setRoles([$data['roles']])
                ->setDateDerniereAction(new DateTime());

            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La modification de l'utilisateur [$utilisateurId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $utilisateurSerealize = $this->serializer->serialize($utilisateur, 'json', ['groups' => 'utilisateur']);
        return new JsonResponse(['message' => 'Utilisateur modifié avec succès', 'utilisateur' => $utilisateurSerealize], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/utilisateurs/{utilisateurId}",
     *     tags={"Utilisateurs"},
     *     summary="Supprimer un utilisateur par ID",
     *     @OA\Parameter(
     *         name="utilisateurId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur à supprimer"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Utilisateur supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     * @Rest\Delete("/api/utilisateurs/{utilisateurId}")
     * @Security(name="Bearer")
     */
    public function deleteUtilisateur(int $utilisateurId): JsonResponse
    {
        try {
            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);

            if (!$utilisateur) {
                return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
            }
            $this->em->remove($utilisateur);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La suppression de l'utilisateur [$utilisateurId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Utilisateur supprimé avec succès'], Response::HTTP_NO_CONTENT);
    }
}
