<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClasseController extends AbstractController
{
    /**
     * @Route("/classes", methods={"POST"})
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Les détails de la classe à créer",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="libelle", type="string"),
     *         @SWG\Property(property="nombreEleves", type="integer"),
     *         @SWG\Property(property="cursusId", type="integer")
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Classe créée avec succès",
     *     @Model(type=Classe::class)
     * )
     */
    public function createClasse(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $classe = new Classe();
        $classe->setLibelle($data['libelle']);
        $classe->setNombreEleves($data['nombreEleves']);

        $cursusId = $data['cursusId'];
        $cursus = $entityManager->getRepository(Cursus::class)->find($cursusId);
        $classe->setCursus($cursus);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($classe);
        $entityManager->flush();

        $classeData = $classe->toArray();

        return new JsonResponse(['message' => 'Classe créée avec succès', 'classe' => $classeData], JsonResponse::HTTP_CREATED);
    }
     /**
     * @Route("/classes/{id}", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Détails de la classe",
     *     @Model(type=Classe::class)
     * )
     */
    public function getClasse(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $classeRepository = $entityManager->getRepository(Classe::class);
        $classe = $classeRepository->find($id);

        if (!$classe) {
            return new JsonResponse(['message' => 'Classe non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        $classeData = $classe->toArray();

        return new JsonResponse($classeData, JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/classes/{id}", methods={"PUT"})
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Les détails de la classe à mettre à jour",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="libelle", type="string"),
     *         @SWG\Property(property="nombreEleves", type="integer"),
     *         @SWG\Property(property="cursusId", type="integer")
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Classe mise à jour avec succès",
     *     @Model(type=Classe::class)
     * )
     */
    public function updateClasse(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $classeRepository = $entityManager->getRepository(Classe::class);
        $classe = $classeRepository->find($id);

        if (!$classe) {
            return new JsonResponse(['message' => 'Classe non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        
        $classe->setLibelle($data['libelle']);
        $classe->setNombreEleves($data['nombreEleves']);

        
        $cursusId = $data['cursusId'];
        $cursus = $entityManager->getRepository(Cursus::class)->find($cursusId);
        $classe->setCursus($cursus);

        $entityManager->persist($classe);
        $entityManager->flush();

        $classeData = $classe->toArray();

        return new JsonResponse(['message' => 'Classe mise à jour avec succès', 'classe' => $classeData], JsonResponse::HTTP_OK);
    }
     /**
     * @Route("/classes/{id}", methods={"DELETE"})
     * @SWG\Response(
     *     response=200,
     *     description="Classe supprimée avec succès"
     * )
     */
    public function deleteClasse(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $classeRepository = $entityManager->getRepository(Classe::class);
        $classe = $classeRepository->find($id);

        if (!$classe) {
            return new JsonResponse(['message' => 'Classe non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($classe);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Classe supprimée avec succès'], JsonResponse::HTTP_OK);
    }
    /**
     * Récupère la liste de toutes les classes.
     *
     * @Route("/classes", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/classes",
     *     summary="Récupère la liste de toutes les classes.",
     *     @SWG\Response(
     *         response=200,
     *         description="Liste de toutes les classes",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=Classe::class, groups={"read"})
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Requête invalide"
     *     )
     * )
     */
    public function getClasses(): JsonResponse
    {
        // Récupérez la liste de toutes les classes depuis le dépôt
        $classes = $this->entityManager->getRepository(Classe::class)->findAll();

        // Convertissez la liste de classes en un tableau de classes pour la réponse JSON
        $classesData = array_map(function ($classe) {
            return $classe->toArray();
        }, $classes);

        // Retournez une réponse JSON avec la liste de toutes les classes
        return new JsonResponse($classesData, JsonResponse::HTTP_OK);
    }
}

