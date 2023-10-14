<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbonnementController extends AbstractController
{

    private $abonnementRepository;
    private $entityManager;

    public function __construct(AbonnementRepository $abonnementRepository, EntityManagerInterface $entityManager)
    {
        $this->abonnementRepository = $abonnementRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="list_abonnements", methods={"GET"})
     *
     * @Nelmio\ApiDoc(
     *     security=false,
     *     output=Abonnement::class,
     *     statusCodes={
     *         200="Returned when successful",
     *     }
     * )
     */
    public function listAbonnements(AbonnementRepository $abonnementRepository): JsonResponse
    {
     
        $abonnements = $abonnementRepository->findAll();

 
        return $this->json($abonnements, JsonResponse::HTTP_OK, [], ['groups' => ['full']]);
    }
    
    /**
     * @Route("", methods={"POST"})
     *
     * @OA\Post(
     *     path="/abonnements",
     *     tags={"Abonnements"},
     *     summary="Créer un nouvel abonnement",
     *     @OA\RequestBody(
     *         @Model(type=AbonnementType::class)
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Abonnement créé avec succès"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createAbonnement(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
 
        $abonnement = new Abonnement();
        
    
        $abonnement->setLibelle($data['libelle']);
        $abonnement->setLibelleTechnique($data['libelle_technique']);
        $abonnement->setPrix($data['prix']);
        
        // Enregistrez l'abonnement dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($abonnement);
        $entityManager->flush();
        
        return new JsonResponse(['message' => 'Abonnement créé avec succès'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/abonnements/{id}", methods={"PUT"})
     *
     * @OA\Put(
     *     path="/abonnements/{id}",
     *     tags={"Abonnements"},
     *     summary="Mettre à jour les détails d'un abonnement par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'abonnement"
     *     ),
     *     @OA\RequestBody(
     *         @Model(type=AbonnementType::class)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonnement mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Abonnement non trouvé"
     *     )
     * )
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAbonnement(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $abonnement = $entityManager->getRepository(Abonnement::class)->find($id);
        
        if (!$abonnement) {
            return new JsonResponse(['message' => 'Abonnement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }
        
      
        $abonnement->setLibelle($data['libelle']);
        $abonnement->setLibelleTechnique($data['libelle_technique']);
        $abonnement->setPrix($data['prix']);
        
        $entityManager->flush();
        
        return new JsonResponse(['message' => 'Abonnement mis à jour avec succès'], JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/abonnements/{id}", methods={"DELETE"})
     *
     * @OA\Delete(
     *     path="/abonnements/{id}",
     *     tags={"Abonnements"},
     *     summary="Supprimer un abonnement par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'abonnement"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonnement supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Abonnement non trouvé"
     *     )
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAbonnement(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $abonnement = $entityManager->getRepository(Abonnement::class)->find($id);
        
        if (!$abonnement) {
            return new JsonResponse(['message' => 'Abonnement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }
        
       
        $entityManager->remove($abonnement);
        $entityManager->flush();
        
        return new JsonResponse(['message' => 'Abonnement supprimé avec succès'], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/subscribe/{etablissementId}/{abonnementId}", methods={"POST"})
     *
     * @OA\Post(
     *     path="/abonnements/subscribe/{etablissementId}/{abonnementId}",
     *     tags={"Abonnements"},
     *     summary="Souscrire un abonnement pour un établissement",
     *     @OA\Parameter(
     *         name="etablissementId",
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
     *         response=201,
     *         description="Abonnement souscrit avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Établissement ou abonnement non trouvé"
     *     )
     * )
     *
     * @param int $etablissementId
     * @param int $abonnementId
     * @param AbonnementRepository $abonnementRepository
     * @return JsonResponse
     */
    public function subscribeAbonnement(Etablissement $etablissement, int $abonnementId): JsonResponse
    {
      
        $abonnement = $this->abonnementRepository->find($abonnementId);

        
        if (!$abonnement) {
            return new JsonResponse(['message' => 'Abonnement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Assurez-vous que l'établissement n'a pas déjà souscrit à un abonnement
        if ($etablissement->getAbonnement() !== null) {
            return new JsonResponse(['message' => 'L\'établissement a déjà souscrit à un abonnement'], JsonResponse::HTTP_BAD_REQUEST);
        }

        
        $etablissement->setAbonnement($abonnement);

     
        $etablissement->setDateAbonnement(new \DateTime());

        $etablissement->setStatutAbonnement(true);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Abonnement souscrit avec succès'], JsonResponse::HTTP_CREATED);
    }
    /**
     * @Route("/cancel/{etablissementId}/{abonnementId}", methods={"DELETE"})
     *
     * @OA\Delete(
     *     path="/abonnements/cancel/{etablissementId}/{abonnementId}",
     *     tags={"Abonnements"},
     *     summary="Annuler un abonnement pour un établissement",
     *     @OA\Parameter(
     *         name="etablissementId",
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
     *         response=404,
     *         description="Établissement ou abonnement non trouvé"
     *     )
     * )
     *
     * @param int $etablissementId
     * @param int $abonnementId
     * @param AbonnementRepository $abonnementRepository
     * @return JsonResponse
     */
    public function cancelAbonnement(Etablissement $etablissement): JsonResponse
    {
       
        $abonnement = $etablissement->getAbonnement();

        if (!$abonnement) {
            return new JsonResponse(['message' => 'L\'établissement n\'a pas d\'abonnement actif'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $etablissement->setAbonnement(null);

        $etablissement->setDateAbonnement(null);

        $etablissement->setStatutAbonnement(false);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Abonnement annulé avec succès'], JsonResponse::HTTP_OK);
    }

}
