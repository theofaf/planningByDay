<?php

namespace App\Controller;

use App\Entity\Batiment;
use App\Entity\Etablissement;
use App\Repository\BatimentRepository;
use App\Service\BatimentService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BatimentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BatimentService $serviceBatiment,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/batiments",
     *     tags={"Batiments"},
     *     summary="Récupère tous les bâtiments",
     *     @OA\Response(
     *          response=200,
     *          description="Tous les bâtiments sont retournés",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Batiment::class, groups={"batiment"}))
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/batiments")
     * @Security(name="Bearer")
     */
    public function getBatiments(): JsonResponse
    {
        try {
            $batiments = $this->em->getRepository(Batiment::class)->findAll();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($batiments, 'json', ['groups' => 'batiment']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/batiments/etablissement/{etablissementId}",
     *     tags={"Batiments"},
     *     summary="Récupère les bâtiments d'un établissement",
     *     @OA\Parameter(
     *          name="etablissementId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de l'établissement"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="La liste des bâtiments d'un établissement est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Batiment::class, groups={"batiment"}))
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
     * @Rest\Get("/api/batiments/etablissement/{etablissementId}")
     * @Security(name="Bearer")
     */
    public function getBatimentsParEtablissementId(int $etablissementId): JsonResponse
    {
        try {
            $etablissement = $this->em->getRepository(Etablissement::class)->find($etablissementId);

            if (!$etablissement) {
                return new JsonResponse(['message' => "L'établissement n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $batiments = $this->em->getRepository(Batiment::class)->findBy(['etablissement' => $etablissement->getId()]);
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($batiments, 'json', ['groups' => 'batiment']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/batiments/recherche-par-filtres",
     *     tags={"Batiments"},
     *     summary="Récupère les bâtiments correspondant aux filtres",
     *     @OA\Parameter(
     *          name="libelle",
     *          @OA\Schema(type="string"),
     *          in="query",
     *          required=false,
     *          description="Libellé correspondant aux bâtiments que l'on souhaite récupérer"
     *
     *      ),
     *     @OA\Parameter(
     *           name="ville",
     *           @OA\Schema(type="string"),
     *           in="query",
     *           required=false,
     *           description="Ville correspondant aux bâtiments que l'on souhaite récupérer"
     *       ),
     *     @OA\Parameter(
     *            name="codePostal",
     *            @OA\Schema(type="string"),
     *            in="query",
     *            required=false,
     *            description="Code postal correspondant aux bâtiments que l'on souhaite récupérer"
     *        ),
     *     @OA\Response(
     *            response=200,
     *            description="La liste des bâtiments correspondant aux filtres est retournée",
     *            @OA\JsonContent(
     *                type="array",
     *                @OA\Items(ref=@Model(type=Batiment::class, groups={"batiment"}))
     *            )
     *       ),
     *     @OA\Response(
     *          response=400,
     *          description="Données invalides"
     *      ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/batiments/recherche-par-filtres")
     * @Security(name="Bearer")
     */
    public function getBatimentsByFiltres(Request $request): JsonResponse
    {
        $libelle = $request->query->get('libelle');
        $ville = $request->query->get('ville');
        $codePostal = $request->query->get('codePostal');

        if (null == $libelle && null == $ville && null == $codePostal) {
            return new JsonResponse(['message' => "Paramètre manquant : Veuillez renseigner au moins : 'libelle ou 'ville' ou 'codePostal'"], Response::HTTP_BAD_REQUEST);
        }

        try {
            $batiments = $this->em->getRepository(Batiment::class)->findBatimentByFiltres($libelle, $ville, $codePostal);
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($batiments, 'json', ['groups' => 'batiment']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/batiments/{batimentId}",
     *     tags={"Batiments"},
     *     summary="Récupère un bâtiment par son ID",
     *     @OA\Parameter(
     *          name="batimentId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID du bâtiment"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="Le bâtiment est retourné",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Batiment::class, groups={"batiment"}))
     *           )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Le bâtiment n'a pas été trouvé"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/batiments/{batimentId}")
     * @Security(name="Bearer")
     */
    public function getBatimentParId(int $batimentId): JsonResponse
    {
        try {
            $batiment = $this->em->getRepository(Batiment::class)->find($batimentId);

            if (!$batiment) {
                return new JsonResponse(['message' => 'Bâtiment non trouvé'], Response::HTTP_NOT_FOUND);
            }
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($batiment, 'json', ['groups' => 'batiment']), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/batiments",
     *     tags={"Batiments"},
     *     summary="Créer un nouvel batiment",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du batiment à créer",
     *         @OA\JsonContent(ref=@Model(type=Batiment::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Batiment créé avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Batiment::class, groups={"batiment"}))
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
     * @Rest\Post("/api/batiments")
     * @Security(name="Bearer")
     */
    public function postBatiment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$this->serviceBatiment->isDataValide($data)) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        $batiment = (new Batiment())
            ->setLibelle($data['libelle'])
            ->setNumVoie($data['numVoie'])
            ->setRue($data['rue'])
            ->setVille($data['ville'])
            ->setCodePostal($data['codePostal'])
            ->setNumeroTel($data['numeroTel'])
        ;

        try {
            $etablissement = $this->em->getRepository(Etablissement::class)->find($data['etablissementId']);
            if (!$etablissement) {
                return new JsonResponse(['message' => 'Établissement non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $batiment->setEtablissement($etablissement);
            $this->em->persist($batiment);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $batimentSerealize = $this->serializer->serialize($batiment, 'json', ['groups' => 'batiment']);
        return new JsonResponse(['message' => 'Bâtiment créé avec succès', 'batiment' => $batimentSerealize], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/batiments/{batimentId}",
     *     tags={"Batiments"},
     *     summary="Mettre à jour les détails d'un batiment par ID",
     *     @OA\Parameter(
     *         name="batimentId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID du batiment"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du batiment à mettre à jour",
     *         @OA\JsonContent(ref=@Model(type=Batiment::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Batiment mis à jour avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Batiment::class, groups={"batiment"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Batiment non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Put("/api/batiments/{batimentId}")
     * @Security(name="Bearer")
     */
    public function putBatiment(int $batimentId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            if (!$this->serviceBatiment->isDataValide($data)) {
                return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
            }

            $batiment = $this->em->getRepository(Batiment::class)->find($batimentId);

            if (!$batiment) {
                return new JsonResponse(['message' => 'Bâtiment non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $batiment
                ->setLibelle($data['libelle'])
                ->setNumVoie($data['numVoie'])
                ->setRue($data['rue'])
                ->setVille($data['ville'])
                ->setCodePostal($data['codePostal'])
                ->setNumeroTel($data['numeroTel'])
            ;
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->em->flush();

        $batimentSerealize = $this->serializer->serialize($batiment, 'json', ['groups' => 'batiment']);
        return new JsonResponse(['message' => 'Bâtiment mis à jour avec succès', 'batiment' => $batimentSerealize], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/batiments/{batimentId}",
     *     tags={"Batiments"},
     *     summary="Supprimer un batiment par ID",
     *     @OA\Parameter(
     *         name="batimentId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID du batiment à supprimer"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Batiment supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Batiment non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     * @Rest\Delete("/api/batiments/{batimentId}")
     * @Security(name="Bearer")
     */
    public function deleteBatiment(int $batimentId): JsonResponse
    {
        $batiment = $this->em->getRepository(Batiment::class)->find($batimentId);

        if (!$batiment) {
            return new JsonResponse(['message' => 'Batiment non trouvé'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->em->remove($batiment);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Batiment supprimé avec succès'], Response::HTTP_NO_CONTENT);
    }
}
