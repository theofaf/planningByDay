<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Etablissement;
use App\Entity\ModuleFormation;
use App\Entity\Salle;
use App\Entity\Statut;
use App\Entity\Utilisateur;
use App\Service\SessionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;
use App\Entity\Session;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SessionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
        private readonly SessionService $serviceSession,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/sessions",
     *     tags={"Sessions"},
     *     summary="Récupère toutes les sessions",
     *     @OA\Response(
     *          response=200,
     *          description="Tous les sessions sont retournées",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Session::class, groups={"session"}))
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/sessions")
     * @Security(name="Bearer")
     */
    public function getSessions(): JsonResponse
    {
        try {
            $sessions = $this->em->getRepository(Session::class)->findAll();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($sessions, 'json', ['groups' => 'session']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/sessions/{sessionId}",
     *     tags={"Sessions"},
     *     summary="Récupère une session par ID",
     *     @OA\Parameter(
     *          name="sessionId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de la session"
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="La session est retournée",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Session::class, groups={"session"}))
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="La session n'a pas été trouvée"
     *      ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/sessions/{sessionId}")
     * @Security(name="Bearer")
     */
    public function getSessionParId(int $sessionId): JsonResponse
    {
        try {
            $session = $this->em->getRepository(Session::class)->find($sessionId);

            if (!$session) {
                return new JsonResponse(['message' => 'Session non trouvée'], Response::HTTP_NOT_FOUND);
            }
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($session, 'json', ['groups' => 'session']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/sessions/etablissement/{etablissementId}",
     *     tags={"Sessions"},
     *     summary="Récupère les sessions d'un établissement par ID",
     *     @OA\Parameter(
     *          name="etablissementId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de l'établissement"
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Les sessions d'un établissement sont retournées",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Session::class, groups={"session"}))
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="L'établissement n'a pas été trouvé"
     *      ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/sessions/etablissement/{etablissementId}")
     * @Security(name="Bearer")
     */
    public function getSessionsParEtablissementId(int $etablissementId): JsonResponse
    {
        try {
            $etablissement = $this->em->getRepository(Etablissement::class)->find($etablissementId);

            if (!$etablissement) {
                return new JsonResponse(['message' => "L'établissement n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $sessions = $this->em->getRepository(Session::class)->getSessionsParEtablissementId($etablissement->getId());
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($sessions, 'json', ['groups' => 'session']), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/sessions",
     *     tags={"Sessions"},
     *     summary="Créer une nouvelle session",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de la session à créer",
     *         @OA\JsonContent(ref=@Model(type=Session::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Session créée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Session::class, groups={"session"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Module formation ou utlisateur ou classe ou salle non trouvée"
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Post("/api/sessions")
     * @Security(name="Bearer")
     */
    public function postSession(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {

            if (!$this->serviceSession->isDataValide($data)) {
                return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);

            }
            $moduleFormation = $this->em->getRepository(ModuleFormation::class)->find($data['moduleFormationId']);
            $classe = $this->em->getRepository(Classe::class)->find($data['classeId']);
            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($data['utilisateurId']);
            $salle = $this->em->getRepository(Salle::class)->find($data['salleId']);
            $statutAttente = $this->em->getRepository(Statut::class)->find(Statut::STATUT_ATTENTE_ID);

            if (!$moduleFormation || !$classe || !$utilisateur || !$salle) {
                return new JsonResponse(['message' => "Le module ou salle ou classe ou utilisateur n'existe pas"], Response::HTTP_NOT_FOUND);
            } elseif (!$statutAttente) {
                return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $session = (new Session())
                ->setDateDebut(new DateTime($data['dateDebut']))
                ->setDateFin(new DateTime($data['dateFin']))
                ->setModuleFormation($moduleFormation)
                ->setUtilisateur($utilisateur)
                ->setClasse($classe)
                ->setSalle($salle)
                ->setStatut($statutAttente)
            ;

            $this->em->persist($session);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $sessionSerialize = $this->serializer->serialize($session, 'json', ['groups' => 'session']);
        return new JsonResponse(['message' => 'Session créée avec succès', 'session' => $sessionSerialize], Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/sessions/{sessionId}",
     *     tags={"Sessions"},
     *     summary="Supprimer une session par ID",
     *     @OA\Parameter(
     *         name="sessionId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de la session à supprimer"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Session supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Session non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     * @Rest\Delete("/api/sessions/{sessionId}")
     * @Security(name="Bearer")
     */
    public function deleteSession(int $sessionId): JsonResponse
    {
        try {
            $session = $this->em->getRepository(Session::class)->find($sessionId);

            if (!$session) {
                return new JsonResponse(['message' => 'Session non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $this->em->remove($session);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Session supprimée avec succès'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *     path="/api/sessions/classe/{classeId}",
     *     tags={"Sessions"},
     *     summary="Récupère les sessions d'une classe",
     *     @OA\Parameter(
     *          name="classeId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de la classe"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="La liste des sessions d'une classe est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Session::class, groups={"session"}))
     *           )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="La classe n'a pas été trouvée"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/sessions/classe/{classeId}")
     * @Security(name="Bearer")
     */
    public function getSessionsParClasseId(int $classeId): JsonResponse
    {
        try {
            $classe = $this->em->getRepository(Classe::class)->find($classeId);

            if (!$classe) {
                return new JsonResponse(['message' => "La classe n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $sessions = $this->em->getRepository(Session::class)->findBy(['classe' => $classe->getId()]);
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($sessions, 'json', ['groups' => 'session']), Response::HTTP_OK);
    }

    /**
     * @OA\Patch(
     *     path="/api/sessions/{sessionId}/utilisateur/{utilisateurId}/choix",
     *     tags={"Sessions"},
     *     summary="Acceptation ou refus d'une session par un utilisateur",
     *     @OA\Parameter(
     *         name="sessionId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de la session"
     *     ),
     *     @OA\Parameter(
     *         name="utilisateurId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur"
     *     ),
     *     @OA\Parameter(
     *           name="choix",
     *           @OA\Schema(type="boolean"),
     *           in="query",
     *           required=true,
     *           description="Booléen indiquant l'acceptation/refus d'une session"
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Session acceptée ou refusée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Session ou utilisateur non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Patch("/api/sessions/{sessionId}/utilisateur/{utilisateurId}/choix")
     * @Security(name="Bearer")
     */
    public function patchSessionAcceptation(int $sessionId, int $utilisateurId, Request $request): JsonResponse
    {
        try {
            $choix = boolval($request->query?->get('choix'));
            $session = $this->em->getRepository(Session::class)->find($sessionId);
            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);

            if (!$session || !$utilisateur) {
                return new JsonResponse(['message' => 'Session ou utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
            }

            if (
                $session->getUtilisateur()->getId() !== $utilisateur->getId()
                || null !== $session->getEstAcceptee()
            ) {
                return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);

            }

            $session->setEstAcceptee($choix);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $res = $choix ? 'acceptée' : 'refusée';
        return new JsonResponse(['message' => "Session $res avec succès"], Response::HTTP_OK);
    }
}