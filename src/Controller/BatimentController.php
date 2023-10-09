<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BatimentController extends AbstractController
{
     /**
     * Crée un nouveau bâtiment.
     *
     * @Route("/create", methods={"POST"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     description="Données du bâtiment à créer",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="nom", type="string", description="Nom du bâtiment"),
     *         @SWG\Property(property="adresse", type="string", description="Adresse du bâtiment"),
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Bâtiment créé avec succès",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message de confirmation"),
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function createBatiment(Request $request): JsonResponse
    {
        // Récupérez les données de la requête JSON
        $data = json_decode($request->getContent(), true);

        // Validez et créez le bâtiment
        $batiment = new Batiment();
        $batiment->setNom($data['nom']); // Assurez-vous d'avoir les champs corrects ici
        // Vous pouvez définir d'autres propriétés du bâtiment ici

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($batiment);
        $entityManager->flush();

        // Retournez une réponse JSON avec le nouveau bâtiment créé
        return new JsonResponse(['message' => 'Bâtiment créé avec succès', 'batiment' => $batiment->toArray()], JsonResponse::HTTP_CREATED);
    }
    /**
     * @Route("/", methods={"GET"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Response(
     *     response=200,
     *     description="Liste de tous les bâtiments",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Batiment::class))
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function listBatiments(): JsonResponse
    {
        // Récupla liste des bâtiments depuis la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $batimentRepository = $entityManager->getRepository(Batiment::class);
        $batiments = $batimentRepository->findAll();

        // Créer un tableau pour stocker les données des bâtiments
        $batimentsData = [];

        // Convertir chaque bâtiment en un tableau de données
        foreach ($batiments as $batiment) {
            $batimentsData[] = $batiment->toArray(); // Assurez-vous d'avoir la méthode toArray() dans votre entité Batiment
        }

        // Retournez une réponse JSON avec la liste des bâtiments
        return new JsonResponse($batimentsData, JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/{id}", methods={"GET"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="ID du bâtiment à afficher"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Détails du bâtiment",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="nom", type="string", description="Nom du bâtiment"),
     *         @SWG\Property(property="adresse", type="string", description="Adresse du bâtiment"),
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Bâtiment non trouvé",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message d'erreur")
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function getBatiment(int $id): JsonResponse
    {
        // Recherche le bâtiment par ID dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $batimentRepository = $entityManager->getRepository(Batiment::class);
        $batiment = $batimentRepository->find($id);

        // Si le bâtiment n'est pas trouvé, retournez une réponse 404
        if (!$batiment) {
            return new JsonResponse(['message' => 'Bâtiment non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Convertir le bâtiment en un tableau de données si nécessaire
        $batimentData = $batiment->toArray(); 

        // Retourne une réponse JSON avec les détails du bâtiment
        return new JsonResponse($batimentData, JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/{id}", methods={"PUT"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="ID du bâtiment à mettre à jour"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     description="Nouvelles données du bâtiment",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="nom", type="string", description="Nouveau nom du bâtiment"),
     *         @SWG\Property(property="adresse", type="string", description="Nouvelle adresse du bâtiment"),
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Bâtiment mis à jour avec succès",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message de confirmation"),
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Bâtiment non trouvé",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message d'erreur")
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function updateBatiment(int $id, Request $request, BatimentRepository $batimentRepository): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $batiment = $batimentRepository->find($id);

        if (!$batiment) {
            return new JsonResponse(['message' => 'Bâtiment non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Mettre à jour les propriétés du bâtiment avec les nouvelles données
        if (isset($data['libelle'])) {
            $batiment->setLibelle($data['libelle']);
        }
        if (isset($data['numVoie'])) {
            $batiment->setNumVoie($data['numVoie']);
        }
        if (isset($data['rue'])) {
            $batiment->setRue($data['rue']);
        }
        if (isset($data['ville'])) {
            $batiment->setVille($data['ville']);
        }
        if (isset($data['codePostal'])) {
            $batiment->setCodePostal($data['codePostal']);
        }
        if (isset($data['numeroTel'])) {
            $batiment->setNumeroTel($data['numeroTel']);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Bâtiment mis à jour avec succès'], JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/{id}", methods={"DELETE"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="ID du bâtiment à supprimer"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Bâtiment supprimé avec succès",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message de confirmation"),
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Bâtiment non trouvé",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message d'erreur")
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function deleteBatiment(int $id, BatimentRepository $batimentRepository): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $batiment = $batimentRepository->find($id);

        if (!$batiment) {
            return new JsonResponse(['message' => 'Bâtiment non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Supprimé le bâtiment de la base de données
        $entityManager->remove($batiment);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Bâtiment supprimé avec succès'], JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/{batimentId}/affecter-salle", methods={"POST"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="batimentId",
     *     in="path",
     *     type="integer",
     *     description="ID du bâtiment auquel affecter une salle"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     description="ID de la salle à affecter au bâtiment",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="salleId", type="integer", description="ID de la salle à affecter"),
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Salle affectée au bâtiment avec succès",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message de confirmation"),
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Bâtiment ou salle non trouvé(s)",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message d'erreur")
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function affecterSalleABatiment(int $batimentId, Request $request, BatimentRepository $batimentRepository, SalleRepository $salleRepository): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $batiment = $batimentRepository->find($batimentId);

        if (!$batiment) {
            return new JsonResponse(['message' => 'Bâtiment non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Récup l'ID de la salle depuis la requête JSON
        $data = json_decode($request->getContent(), true);
        $salleId = $data['salleId'];

        // Recherche la salle par ID dans la base de données
        $salle = $salleRepository->find($salleId);

        if (!$salle) {
            return new JsonResponse(['message' => 'Salle non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Affecte la salle au bâtiment
        $batiment->addSalle($salle);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Salle affectée au bâtiment avec succès'], JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/{batimentId}/desaffecter-salle", methods={"POST"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="batimentId",
     *     in="path",
     *     type="integer",
     *     description="ID du bâtiment auquel désaffecter une salle"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     description="ID de la salle à désaffecter du bâtiment",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="salleId", type="integer", description="ID de la salle à désaffecter"),
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Salle désaffectée du bâtiment avec succès",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message de confirmation"),
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Bâtiment ou salle non trouvé(s)",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message d'erreur")
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function desaffecterSalleDeBatiment(int $batimentId, Request $request, BatimentRepository $batimentRepository, SalleRepository $salleRepository): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $batiment = $batimentRepository->find($batimentId);

        if (!$batiment) {
            return new JsonResponse(['message' => 'Bâtiment non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Récup l'ID de la salle depuis la requête JSON
        $data = json_decode($request->getContent(), true);
        $salleId = $data['salleId'];

        // Recherche la salle par ID dans la base de données
        $salle = $salleRepository->find($salleId);

        if (!$salle) {
            return new JsonResponse(['message' => 'Salle non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Désaffecte la salle du bâtiment
        $batiment->removeSalle($salle);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Salle désaffectée du bâtiment avec succès'], JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/{id}/salles", methods={"GET"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="ID du bâtiment"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Liste des salles du bâtiment",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Salle::class, groups={"full"}))
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Bâtiment non trouvé",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message d'erreur")
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function getSallesByBatiment(int $id, BatimentRepository $batimentRepository): JsonResponse
    {
        $batiment = $batimentRepository->find($id);

        if (!$batiment) {
            return new JsonResponse(['message' => 'Bâtiment non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $salles = $batiment->getSalles();


        return $this->json($salles, JsonResponse::HTTP_OK, [], ['groups' => ['full']]);
    }
    /**
     * @Route("/search", methods={"GET"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="nom",
     *     in="query",
     *     type="string",
     *     description="Nom du bâtiment à rechercher"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Liste des bâtiments correspondants à la recherche",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Batiment::class, groups={"light"}))
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Paramètre 'nom' manquant"
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function searchBatiments(Request $request, BatimentRepository $batimentRepository): JsonResponse
    {
        $nom = $request->query->get('nom');

        if (!$nom) {
            return new JsonResponse(['message' => 'Paramètre \'nom\' manquant'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Effectue la recherche des bâtiments par nom dans la base de données
        $resultats = $batimentRepository->searchByNom($nom);

        return $this->json($resultats, JsonResponse::HTTP_OK, [], ['groups' => ['light']]);
    }
    /**
     * @Route("/{id}/salles/disponibilites", methods={"GET"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="ID du bâtiment"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Liste des salles du bâtiment avec leurs disponibilités",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=SalleAvecDisponibilite::class, groups={"full"}))
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Bâtiment non trouvé",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message d'erreur")
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function getSallesWithDisponibilitesByBatiment(int $id, BatimentRepository $batimentRepository): JsonResponse
    {
        // Recherche le bâtiment par ID dans la base de données
        $batiment = $batimentRepository->find($id);

        // Si le bâtiment n'est pas trouvé, retourne une réponse 404
        if (!$batiment) {
            return new JsonResponse(['message' => 'Bâtiment non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Récup la liste des salles du bâtiment avec leurs disponibilités
        $sallesAvecDisponibilites = $batiment->getSallesWithDisponibilites();

        return $this->json($sallesAvecDisponibilites, JsonResponse::HTTP_OK, [], ['groups' => ['full']]);
    }
    /**
     * @Route("/{id}/cours", methods={"GET"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="ID du bâtiment"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Liste des cours planifiés dans le bâtiment",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Cours::class, groups={"full"}))
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Bâtiment non trouvé",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message d'erreur")
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function getCoursByBatiment(int $id, BatimentRepository $batimentRepository): JsonResponse
    {
        // Recherche le bâtiment par ID dans la base de données
        $batiment = $batimentRepository->find($id);

        // Si le bâtiment n'est pas trouvé, retourne une réponse 404
        if (!$batiment) {
            return new JsonResponse(['message' => 'Bâtiment non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Récup la liste des cours planifiés dans le bâtiment 
        $cours = $batiment->getCours();


        return $this->json($cours, JsonResponse::HTTP_OK, [], ['groups' => ['full']]);
    }
    /**
     * @Route("/{id}/salles", methods={"POST"})
     *
     * @SWG\Tag(name="Batiment")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="ID du bâtiment"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     description="Données de la salle à créer",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="nom", type="string", description="Nom de la salle"),
     *         @SWG\Property(property="capacite", type="integer", description="Capacité de la salle")
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Salle créée avec succès",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message de confirmation"),
     *         @SWG\Property(property="salle", ref=@Model(type=Salle::class, groups={"light"}))
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Bâtiment non trouvé",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message d'erreur")
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Données de la salle non valides",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="message", type="string", description="Message d'erreur")
     *     )
     * )
     * @Security("is_granted('ROLE_RESPONSABLE_PLANNING')")
     * @NelmioSecurity(name="Bearer")
     */
    public function createSalleInBatiment(int $id, Request $request, BatimentRepository $batimentRepository): JsonResponse
    {
        // Recherche le bâtiment par ID dans la base de données
        $batiment = $batimentRepository->find($id);

        // Si le bâtiment n'est pas trouvé, retourne une réponse 404
        if (!$batiment) {
            return new JsonResponse(['message' => 'Bâtiment non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Récupére les données de la requête JSON
        $data = json_decode($request->getContent(), true);

        // Crée la salle dans le bâtiment
        $salle = new Salle();
    

        // associe la salle au bâtiment
        $salle->setBatiment($batiment);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($salle);
        $entityManager->flush();

        // Retourne une réponse JSON avec un message de confirmation et les détails de la salle créée
        return $this->json(['message' => 'Salle créée avec succès', 'salle' => $salle], JsonResponse::HTTP_CREATED, [], ['groups' => ['light']]);
    }
}
