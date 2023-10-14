<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatutController extends AbstractController
{
    /**
     * Créer un nouveau statut.
     *
     * @Route("/statuts", methods={"POST"})
     *
     * @SWG\Post(
     *     path="/statuts",
     *     summary="Créer un nouveau statut.",
     *     @SWG\Parameter(
     *         name="libelle",
     *         in="body",
     *         @SWG\Schema(type="string"),
     *         description="Libellé du statut"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Statut créé avec succès"
     *     )
     * )
     */
    public function createStatut(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $statut = new Statut();
        $statut->setLibelle($data['libelle']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($statut);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Statut créé avec succès', 'statut' => $statut->toArray()], JsonResponse::HTTP_CREATED);
    }
    /**
     * Mettre à jour un statut par ID.
     *
     * @Route("/statuts/{id}", methods={"PUT"})
     *
     * @SWG\Put(
     *     path="/statuts/{id}",
     *     summary="Mettre à jour un statut par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID du statut"
     *     ),
     *     @SWG\Parameter(
     *         name="libelle",
     *         in="body",
     *         @SWG\Schema(type="string"),
     *         description="Nouveau libellé du statut"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Statut mis à jour avec succès"
     *     )
     * )
     */
    public function updateStatut(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $statutRepository = $entityManager->getRepository(Statut::class);
        $statut = $statutRepository->find($id);

        if (!$statut) {
            return new JsonResponse(['message' => 'Statut non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $statut->setLibelle($data['libelle']);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Statut mis à jour avec succès', 'statut' => $statut->toArray()], JsonResponse::HTTP_OK);
    }
    /**
     * Supprimer un statut par ID.
     *
     * @Route("/statuts/{id}", methods={"DELETE"})
     *
     * @SWG\Delete(
     *     path="/statuts/{id}",
     *     summary="Supprimer un statut par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID du statut"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Statut supprimé avec succès"
     *     )
     * )
     */
    public function deleteStatut(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $statutRepository = $entityManager->getRepository(Statut::class);
        $statut = $statutRepository->find($id);

        if (!$statut) {
            return new JsonResponse(['message' => 'Statut non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($statut);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
    /**
     * Récupérer un statut par ID.
     *
     * @Route("/statuts/{id}", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/statuts/{id}",
     *     summary="Récupérer un statut par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID du statut"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Statut récupéré avec succès"
     *     )
     * )
     */
    public function getStatut(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $statutRepository = $entityManager->getRepository(Statut::class);
        $statut = $statutRepository->find($id);

        if (!$statut) {
            return new JsonResponse(['message' => 'Statut non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($statut->toArray(), JsonResponse::HTTP_OK);
    }
    /**
     * Récupérer tous les statuts.
     *
     * @Route("/statuts", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/statuts",
     *     summary="Récupérer tous les statuts.",
     *     @SWG\Response(
     *         response=200,
     *         description="Statuts récupérés avec succès"
     *     )
     * )
     */
    public function getAllStatuts(): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $statutRepository = $entityManager->getRepository(Statut::class);
        $statuts = $statutRepository->findAll();

        $statutsData = [];

        foreach ($statuts as $statut) {
            $statutsData[] = $statut->toArray();
        }

        return new JsonResponse($statutsData, JsonResponse::HTTP_OK);
}

}
