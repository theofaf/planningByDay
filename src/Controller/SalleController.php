<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SalleController extends AbstractController
{
    /**
     * Récupère une salle par ID.
     *
     * @Route("/salles/{id}", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/salles/{id}",
     *     summary="Récupère une salle par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID de la salle à récupérer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Détails de la salle",
     *         @SWG\Schema(ref=@Model(type=Salle::class, groups={"read"}))
     *     )
     * )
     */
    public function getSalle(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salleRepository = $entityManager->getRepository(Salle::class);
        $salle = $salleRepository->find($id);

        if (!$salle) {
            return new JsonResponse(['message' => 'Salle non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        $salleData = $salle->toArray();

        return new JsonResponse($salleData, JsonResponse::HTTP_OK);
    }
    /**
     * Met à jour une salle par ID.
     *
     * @Route("/salles/{id}", methods={"PUT"})
     *
     * @SWG\Put(
     *     path="/salles/{id}",
     *     summary="Met à jour une salle par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID de la salle à mettre à jour"
     *     ),
     *     @SWG\Parameter(
     *         name="request",
     *         in="body",
     *         @SWG\Schema(ref=@Model(type=Salle::class, groups={"update"}))
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Salle mise à jour avec succès",
     *         @SWG\Schema(ref=@Model(type=Salle::class, groups={"read"}))
     *     )
     * )
     */
    public function updateSalle(int $id, Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salleRepository = $entityManager->getRepository(Salle::class);
        $salle = $salleRepository->find($id);

        if (!$salle) {
            return new JsonResponse(['message' => 'Salle non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['libelle'])) {
            $salle->setLibelle($data['libelle']);
        }

        if (isset($data['nbPlace'])) {
            $salle->setNbPlace($data['nbPlace']);
        }

        if (isset($data['equipementInfo'])) {
            $salle->setEquipementInfo($data['equipementInfo']);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Salle mise à jour avec succès', 'salle' => $salle->toArray()], JsonResponse::HTTP_OK);
    }
    /**
     * Crée une nouvelle salle.
     *
     * @Route("/salles", methods={"POST"})
     *
     * @SWG\Post(
     *     path="/salles",
     *     summary="Crée une nouvelle salle.",
     *     @SWG\Parameter(
     *         name="request",
     *         in="body",
     *         @SWG\Schema(ref=@Model(type=Salle::class, groups={"create"}))
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Salle créée avec succès",
     *         @SWG\Schema(ref=@Model(type=Salle::class, groups={"read"}))
     *     )
     * )
     */
    public function createSalle(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $salle = new Salle();

        if (isset($data['libelle'])) {
            $salle->setLibelle($data['libelle']);
        }

        if (isset($data['nbPlace'])) {
            $salle->setNbPlace($data['nbPlace']);
        }

        if (isset($data['equipementInfo'])) {
            $salle->setEquipementInfo($data['equipementInfo']);
        }

        // Remplissez d'autres propriétés si nécessaire

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($salle);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Salle créée avec succès', 'salle' => $salle->toArray()], JsonResponse::HTTP_CREATED);
    }
    /**
     * Récupère toutes les salles.
     *
     * @Route("/salles", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/salles",
     *     summary="Récupère toutes les salles.",
     *     @SWG\Response(
     *         response=200,
     *         description="Liste de toutes les salles",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=Salle::class, groups={"read"}))
     *         )
     *     )
     * )
     */
    public function getAllSalles(): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salleRepository = $entityManager->getRepository(Salle::class);
        $salles = $salleRepository->findAll();

        $sallesData = [];
        foreach ($salles as $salle) {
            $sallesData[] = $salle->toArray();
        }

        return new JsonResponse($sallesData, JsonResponse::HTTP_OK);
    }
    /**
     * Supprime une salle par ID.
     *
     * @Route("/salles/{id}", methods={"DELETE"})
     *
     * @SWG\Delete(
     *     path="/salles/{id}",
     *     summary="Supprime une salle par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID de la salle à supprimer"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Salle supprimée avec succès"
     *     )
     * )
     */
    public function deleteSalle(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salleRepository = $entityManager->getRepository(Salle::class);
        $salle = $salleRepository->find($id);

        if (!$salle) {
            return new JsonResponse(['message' => 'Salle non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($salle);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
