<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    /**
     * Crée une nouvelle session.
     *
     * @Route("/sessions", methods={"POST"})
     *
     * @SWG\Post(
     *     path="/sessions",
     *     summary="Crée une nouvelle session.",
     *     @SWG\RequestBody(
     *         @SWG\JsonContent(
     *             type="object",
     *             required={"dateDebut", "dateFin", "moduleFormation", "utilisateur", "classe", "salle"},
     *             @SWG\Property(type="string", property="dateDebut", example="2023-10-10T09:00:00"),
     *             @SWG\Property(type="string", property="dateFin", example="2023-10-10T12:00:00"),
     *             @SWG\Property(type="integer", property="moduleFormation", example=1),
     *             @SWG\Property(type="integer", property="utilisateur", example=1),
     *             @SWG\Property(type="integer", property="classe", example=1),
     *             @SWG\Property(type="integer", property="salle", example=1)
     *         )
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Session créée avec succès"
     *     )
     * )
     */
    public function createSession(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validez et traitez les données, puis créez une nouvelle session.

        // Exemple de validation des données :
        $session = new Session();
        $session->setDateDebut(new \DateTime($data['dateDebut']));
        $session->setDateFin(new \DateTime($data['dateFin']));
        $entityManager = $this->getDoctrine()->getManager();
        $moduleFormation = $entityManager->getRepository(ModuleFormation::class)->find($data['moduleFormation']);
        $session->setModuleFormation($moduleFormation);
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($data['utilisateur']);
        $session->setUtilisateur($utilisateur);
        $classe = $entityManager->getRepository(Classe::class)->find($data['classe']);
        $session->setClasse($classe);
        $salle = $entityManager->getRepository(Salle::class)->find($data['salle']);
        $session->setSalle($salle);

        $entityManager->persist($session);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Session créée avec succès', 'session' => $session->toArray()], JsonResponse::HTTP_CREATED);
    }
    /**
     * Récupère une session par ID.
     *
     * @Route("/sessions/{id}", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/sessions/{id}",
     *     summary="Récupère une session par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID de la session à récupérer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Session récupérée avec succès"
     *     )
     * )
     */
    public function getSession(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $sessionRepository = $entityManager->getRepository(Session::class);
        $session = $sessionRepository->find($id);

        if (!$session) {
            return new JsonResponse(['message' => 'Session non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        $sessionData = $session->toArray();

        return new JsonResponse($sessionData, JsonResponse::HTTP_OK);
    }
    /**
     * Met à jour une session par ID.
     *
     * @Route("/sessions/{id}", methods={"PUT"})
     *
     * @SWG\Put(
     *     path="/sessions/{id}",
     *     summary="Met à jour une session par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID de la session à mettre à jour"
     *     ),
     *     @SWG\RequestBody(
     *         @SWG\JsonContent(
     *             type="object",
     *             required={"dateDebut", "dateFin", "moduleFormation", "utilisateur", "classe", "salle"},
     *             @SWG\Property(type="string", property="dateDebut", example="2023-10-10T09:00:00"),
     *             @SWG\Property(type="string", property="dateFin", example="2023-10-10T12:00:00"),
     *             @SWG\Property(type="integer", property="moduleFormation", example=1),
     *             @SWG\Property(type="integer", property="utilisateur", example=1),
     *             @SWG\Property(type="integer", property="classe", example=1),
     *             @SWG\Property(type="integer", property="salle", example=1)
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Session mise à jour avec succès"
     *     )
     * )
     */
    public function updateSession(int $id, Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $sessionRepository = $entityManager->getRepository(Session::class);
        $session = $sessionRepository->find($id);

        if (!$session) {
            return new JsonResponse(['message' => 'Session non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Validez et traitez les données, puis mettez à jour la session.

        // Exemple de mise à jour des données :
        $session->setDateDebut(new \DateTime($data['dateDebut']));
        $session->setDateFin(new \DateTime($data['dateFin']));
        $moduleFormation = $entityManager->getRepository(ModuleFormation::class)->find($data['moduleFormation']);
        $session->setModuleFormation($moduleFormation);
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($data['utilisateur']);
        $session->setUtilisateur($utilisateur);
        $classe = $entityManager->getRepository(Classe::class)->find($data['classe']);
        $session->setClasse($classe);
        $salle = $entityManager->getRepository(Salle::class)->find($data['salle']);
        $session->setSalle($salle);

        $entityManager->flush();

        return new JsonResponse(['message' => 'Session mise à jour avec succès', 'session' => $session->toArray()], JsonResponse::HTTP_OK);
    }
    /**
     * Supprime une session par ID.
     *
     * @Route("/sessions/{id}", methods={"DELETE"})
     *
     * @SWG\Delete(
     *     path="/sessions/{id}",
     *     summary="Supprime une session par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID de la session à supprimer"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Session supprimée avec succès"
     *     )
     * )
     */
    public function deleteSession(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $sessionRepository = $entityManager->getRepository(Session::class);
        $session = $sessionRepository->find($id);

        if (!$session) {
            return new JsonResponse(['message' => 'Session non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($session);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
    /**
     * Récupère la liste de toutes les sessions.
     *
     * @Route("/sessions", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/sessions",
     *     summary="Récupère la liste de toutes les sessions.",
     *     @SWG\Response(
     *         response=200,
     *         description="Liste de sessions récupérée avec succès"
     *     )
     * )
     */
    public function getSessions(): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $sessionRepository = $entityManager->getRepository(Session::class);
        $sessions = $sessionRepository->findAll();
        $sessionsData = [];

        foreach ($sessions as $session) {
            $sessionsData[] = $session->toArray();
        }

        return new JsonResponse($sessionsData, JsonResponse::HTTP_OK);
    }
    /**
     * Récupère toutes les sessions d'une classe par ID.
     *
     * @Route("/classes/{id}/sessions", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/classes/{id}/sessions",
     *     summary="Récupère toutes les sessions d'une classe par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID de la classe"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Sessions de la classe récupérées avec succès"
     *     )
     * )
     */
    public function getClassSessions(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $classRepository = $entityManager->getRepository(Classe::class);
        $class = $classRepository->find($id);

        if (!$class) {
            return new JsonResponse(['message' => 'Classe non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        $sessions = $class->getSessions();
        $sessionsData = [];

        foreach ($sessions as $session) {
            $sessionsData[] = $session->toArray();
        }

        return new JsonResponse($sessionsData, JsonResponse::HTTP_OK);
    }
}
