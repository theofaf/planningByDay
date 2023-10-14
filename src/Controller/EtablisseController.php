<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtablisseController extends AbstractController
{
    /**
     * Crée un nouvel établissement.
     *
     * @Route("/etablissements", methods={"POST"})
     *
     * @SWG\Post(
     *     path="/etablissements",
     *     summary="Crée un nouvel établissement.",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @Model(type=Etablissement::class, groups={"write"})
     *         )
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Établissement créé avec succès",
     *         @Model(type=Etablissement::class, groups={"read"})
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Requête invalide"
     *     )
     * )
     */
    public function createEtablissement(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $etablissement = new Etablissement();

 
        $etablissement->setLibelle($data['libelle']);
        $etablissement->setNumVoie($data['numVoie']);
        $etablissement->setRue($data['rue']);
        $etablissement->setVille($data['ville']);
        $etablissement->setCodePostal($data['codePostal']);
        $etablissement->setNumeroTel($data['numeroTel']);
        $etablissement->setStatutAbonnement($data['statutAbonnement']);

        $abonnement = $this->entityManager->getRepository(Abonnement::class)->find($data['abonnementId']);
        $etablissement->setAbonnement($abonnement);

 
        $this->entityManager->persist($etablissement);
        $this->entityManager->flush();

        $etablissementData = $etablissement->toArray();

        return new JsonResponse(['message' => 'Établissement créé avec succès', 'etablissement' => $etablissementData], JsonResponse::HTTP_CREATED);
    }

    /**
     * Met à jour un établissement existant.
     *
     * @Route("/etablissements/{id}", methods={"PUT"})
     *
     * @SWG\Put(
     *     path="/etablissements/{id}",
     *     summary="Met à jour un établissement existant.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID de l'établissement",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @Model(type=Etablissement::class, groups={"write"})
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Établissement mis à jour avec succès",
     *         @Model(type=Etablissement::class, groups={"read"})
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Requête invalide"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Établissement non trouvé"
     *     )
     * )
     */
    public function updateEtablissement(int $id, Request $request): JsonResponse
    {
    
        $etablissement = $this->entityManager->getRepository(Etablissement::class)->find($id);

        if (!$etablissement) {
            return new JsonResponse(['message' => 'Établissement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

 
        $data = json_decode($request->getContent(), true);

        $etablissement->setLibelle($data['libelle']);
        $etablissement->setNumVoie($data['numVoie']);
        $etablissement->setRue($data['rue']);
        $etablissement->setVille($data['ville']);
        $etablissement->setCodePostal($data['codePostal']);
        $etablissement->setNumeroTel($data['numeroTel']);
        $etablissement->setStatutAbonnement($data['statutAbonnement']);

    
        $abonnement = $this->entityManager->getRepository(Abonnement::class)->find($data['abonnementId']);
        $etablissement->setAbonnement($abonnement);


        $this->entityManager->flush();


        $etablissementData = $etablissement->toArray();

        return new JsonResponse(['message' => 'Établissement mis à jour avec succès', 'etablissement' => $etablissementData], JsonResponse::HTTP_OK);
    }

    /**
     * Supprime un établissement par son ID.
     *
     * @Route("/etablissements/{id}", methods={"DELETE"})
     *
     * @SWG\Delete(
     *     path="/etablissements/{id}",
     *     summary="Supprime un établissement par son ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID de l'établissement",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Établissement supprimé avec succès"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Établissement non trouvé"
     *     )
     * )
     */
    public function deleteEtablissement(int $id): JsonResponse
    {

        $etablissement = $this->entityManager->getRepository(Etablissement::class)->find($id);

        if (!$etablissement) {
            return new JsonResponse(['message' => 'Établissement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }


        $this->entityManager->remove($etablissement);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Établissement supprimé avec succès'], JsonResponse::HTTP_OK);
    }
    /**
     * Récupère un établissement par son ID.
     *
     * @Route("/etablissements/{id}", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/etablissements/{id}",
     *     summary="Récupère un établissement par son ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID de l'établissement",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Établissement trouvé",
     *         @Model(type=Etablissement::class, groups={"read"})
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Établissement non trouvé",
     *     )
     * )
     */
    public function getEtablissement(int $id): JsonResponse
    {

        $etablissement = $this->entityManager->getRepository(Etablissement::class)->find($id);

        if (!$etablissement) {
            return new JsonResponse(['message' => 'Établissement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }


        $etablissementData = $etablissement->toArray();

        return new JsonResponse($etablissementData, JsonResponse::HTTP_OK);
    }
    /**
     * Récupère la liste de tous les établissements.
     *
     * @Route("/etablissements", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/etablissements",
     *     summary="Récupère la liste de tous les établissements.",
     *     @SWG\Response(
     *         response=200,
     *         description="Liste de tous les établissements",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=Etablissement::class, groups={"read"})
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Requête invalide"
     *     )
     * )
     */
    public function getEtablissements(): JsonResponse
    {

        $etablissements = $this->entityManager->getRepository(Etablissement::class)->findAll();


        $etablissementsData = array_map(function ($etablissement) {
            return $etablissement->toArray();
        }, $etablissements);


        return new JsonResponse($etablissementsData, JsonResponse::HTTP_OK);
    }

}
