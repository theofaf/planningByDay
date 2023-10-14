<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleFormationController extends AbstractController
{
    /**
     * Ajoute un nouveau ModuleFormation.
     *
     * @Route("/modules-formation", methods={"POST"})
     *
     * @SWG\Post(
     *     path="/modules-formation",
     *     summary="Ajoute un nouveau ModuleFormation.",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @Model(type=ModuleFormation::class, groups={"write"})
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="ModuleFormation ajouté avec succès",
     *         @Model(type=ModuleFormation::class, groups={"read"})
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Données invalides"
     *     )
     * )
     */
    public function createModuleFormation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $moduleFormation = new ModuleFormation();

        if (isset($data['libelle'])) {
            $moduleFormation->setLibelle($data['libelle']);
        }

        if (isset($data['duree'])) {
            $duree = new \DateTimeImmutable($data['duree']);
            $moduleFormation->setDuree($duree);
        }


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($moduleFormation);
        $entityManager->flush();

        return new JsonResponse(['message' => 'ModuleFormation ajouté avec succès', 'moduleFormation' => $moduleFormation->toArray()], JsonResponse::HTTP_CREATED);
    }  
    /**
     * Récupère un ModuleFormation par ID.
     *
     * @Route("/modules-formation/{id}", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/modules-formation/{id}",
     *     summary="Récupère un ModuleFormation par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="ModuleFormation trouvé",
     *         @Model(type=ModuleFormation::class, groups={"read"})
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="ModuleFormation non trouvé"
     *     )
     * )
     */
    public function getModuleFormation(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $moduleFormationRepository = $entityManager->getRepository(ModuleFormation::class);
        $moduleFormation = $moduleFormationRepository->find($id);

        if (!$moduleFormation) {
            return new JsonResponse(['message' => 'ModuleFormation non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $moduleFormationData = $moduleFormation->toArray();

        return new JsonResponse($moduleFormationData, JsonResponse::HTTP_OK);
    }
    /**
     * Met à jour un ModuleFormation par ID.
     *
     * @Route("/modules-formation/{id}", methods={"PUT"})
     *
     * @SWG\Put(
     *     path="/modules-formation/{id}",
     *     summary="Met à jour un ModuleFormation par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @Model(type=ModuleFormation::class, groups={"write"})
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="ModuleFormation mis à jour avec succès",
     *         @Model(type=ModuleFormation::class, groups={"read"})
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="ModuleFormation non trouvé"
     *     )
     * )
     */
    public function updateModuleFormation(int $id, Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $moduleFormationRepository = $entityManager->getRepository(ModuleFormation::class);
        $moduleFormation = $moduleFormationRepository->find($id);

        if (!$moduleFormation) {
            return new JsonResponse(['message' => 'ModuleFormation non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['libelle'])) {
            $moduleFormation->setLibelle($data['libelle']);
        }

        if (isset($data['duree'])) {

            $duree = new \DateTimeImmutable($data['duree']);
            $moduleFormation->setDuree($duree);
        }


        $entityManager->flush();

        return new JsonResponse(['message' => 'ModuleFormation mis à jour avec succès', 'moduleFormation' => $moduleFormation->toArray()], JsonResponse::HTTP_OK);
    }
    /**
     * Supprime un ModuleFormation par ID.
     *
     * @Route("/modules-formation/{id}", methods={"DELETE"})
     *
     * @SWG\Delete(
     *     path="/modules-formation/{id}",
     *     summary="Supprime un ModuleFormation par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="ModuleFormation supprimé avec succès"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="ModuleFormation non trouvé"
     *     )
     * )
     */
    public function deleteModuleFormation(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $moduleFormationRepository = $entityManager->getRepository(ModuleFormation::class);
        $moduleFormation = $moduleFormationRepository->find($id);

        if (!$moduleFormation) {
            return new JsonResponse(['message' => 'ModuleFormation non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($moduleFormation);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
    /**
     * Récupère tous les ModuleFormation.
     *
     * @Route("/modules-formation", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/modules-formation",
     *     summary="Récupère tous les ModuleFormation.",
     *     @SWG\Response(
     *         response=200,
     *         description="Liste de tous les ModuleFormation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=ModuleFormation::class, groups={"read"}))
     *         )
     *     )
     * )
     */
    public function getAllModuleFormation(): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $moduleFormationRepository = $entityManager->getRepository(ModuleFormation::class);
        $moduleFormations = $moduleFormationRepository->findAll();

        $moduleFormationsData = [];
        foreach ($moduleFormations as $moduleFormation) {
            $moduleFormationsData[] = $moduleFormation->toArray();
        }

        return new JsonResponse($moduleFormationsData, JsonResponse::HTTP_OK);
    }
}
