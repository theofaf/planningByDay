<?php

namespace App\Controller;

use App\Entity\Batiment;
use App\Repository\BatimentRepository;
use App\Service\SalleService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Salle;
use App\Entity\Etablissement;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\SerializerInterface;

class SalleController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
        private readonly SalleService $serviceSalle,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/salles",
     *     tags={"Salles"},
     *     summary="Récupère toutes les salles",
     *     @OA\Response(
     *          response=200,
     *          description="Tous les salles sont retournés",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Salle::class, groups={"salles"}))
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/salles")
     * @Security(name="Bearer")
     */
    public function getSalles(): JsonResponse
    {
        try {
            $salles = $this->em->getRepository(Salle::class)->findAll();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($salles, 'json', ['groups' => 'salle']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/salles/{salleId}",
     *     tags={"Salles"},
     *     summary="Récupère une salle par son ID",
     *     @OA\Parameter(
     *          name="salleId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de la salle"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="La salle est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Salle::class, groups={"Salle"}))
     *           )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="La salle n'a pas été trouvée"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/salles/{salleId}")
     * @Security(name="Bearer")
     */
    public function getSalleParId(int $salleId): JsonResponse
    {
        try {
            $salle = $this->em->getRepository(Salle::class)->find($salleId);

            if (!$salle) {
                return new JsonResponse(['message' => 'Salle non trouvé'], Response::HTTP_NOT_FOUND);
            }
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($salle, 'json', ['groups' => 'salle']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/salles/batiment/{batimentId}",
     *     tags={"Salles"},
     *     summary="Récupère les salles d'un bâtiment",
     *     @OA\Parameter(
     *          name="batimentId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID du bâtiment"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="La liste des salles d'un bâtiment est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Salle::class, groups={"salle"}))
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
     * @Rest\Get("/api/salles/batiment/{batimentId}")
     * @Security(name="Bearer")
     */
    public function getSallesByBatiment(int $batimentId): JsonResponse
    {
        try {
            $batiment = $this->em->getRepository(Batiment::class)->find($batimentId);

            if (!$batiment) {
                return new JsonResponse(['message' => "Le bâtiment n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $salles = $this->em->getRepository(Salle::class)->findBy(['batiment' => $batiment->getId()]);
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($salles, 'json', ['groups' => 'salle']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/salles/batiment/{batimentId}/disponibilites",
     *     tags={"Batiments"},
     *     summary="Récupère les salles disponibles sur une plage de date",
     *     @OA\Parameter(
     *          name="batimentId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID du bâtiment"
     *      ),
     *     @OA\Parameter(
     *           name="dateDebut",
     *           @OA\Schema(type="datetime"),
     *           in="query",
     *           required=true,
     *           description="Date de début de disponibité"
     *       ),
     *     @OA\Parameter(
     *            name="dateFin",
     *            @OA\Schema(type="datetime"),
     *            in="query",
     *            required=true,
     *            description="Date de fin de disponibité"
     *        ),
     *     @OA\Response(
     *           response=200,
     *           description="La liste des salles disponible est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Salle::class, groups={"salle"}))
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
     * @Rest\Get("/api/salles/batiment/{batimentId}/disponibilites")
     * @Security(name="Bearer")
     */
    public function getSallesByBatimentWithDisponibilites(int $batimentId, Request $request): JsonResponse
    {
        $dateDebut = DateTime::createFromFormat('Y-m-d',$request->query?->get('dateDebut'));
        $dateFin = DateTime::createFromFormat('Y-m-d',$request->query?->get('dateFin'));

        try {
            $batiment = $this->em->getRepository(Batiment::class)->find($batimentId);

            if (!$batiment) {
                return new JsonResponse(['message' => 'Bâtiment non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $sallesAvecDisponibilites = $this->em->getRepository(Salle::class)->getSallesWithDisponibilites(
                $batiment,
                $dateDebut,
                $dateFin,
            );
        } catch (Exception $e) {
            var_dump($e);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($sallesAvecDisponibilites, 'json', ['groups' => 'salle']), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/salles",
     *     tags={"Salles"},
     *     summary="Créer une nouvelle salle",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de la salle à créer",
     *         @OA\JsonContent(ref=@Model(type=Salle::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Salle créé avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Salle::class, groups={"salle"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Bâtiment non trouvé"
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Post("/api/salles")
     * @Security(name="Bearer")
     */
    public function postSalle(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$this->serviceSalle->isDataValide($data)) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        $salle = (new Salle())
            ->setLibelle($data['libelle'])
            ->setEquipementInfo($data['equipementInfo'])
            ->setNbPlace($data['nbPlace'])
        ;

        try {
            $batiment = $this->em->getRepository(Batiment::class)->find($data['batimentId']);
            if (!$batiment) {
                return new JsonResponse(['message' => 'Établissement non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $salle->setBatiment($batiment);
            $this->em->persist($salle);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $salleSerealize = $this->serializer->serialize($salle, 'json', ['groups' => 'salle']);
        return new JsonResponse(['message' => 'Salle créé avec succès', 'batiment' => $salleSerealize], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/salles/{salleId}",
     *     tags={"Salles"},
     *     summary="Modifier une salle par son ID",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de la salle à modifier",
     *         @OA\JsonContent(ref=@Model(type=Salle::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Salle modifié avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Salle::class, groups={"salle"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Post("/api/salles/{salleId}")
     * @Security(name="Bearer")
     */
    public function putSalle(int $salleId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $salle = $this->em->getRepository(Salle::class)->find($salleId);

            if (!$salle) {
                return new JsonResponse(['message' => 'Salle non trouvée'], Response::HTTP_NOT_FOUND);
            }

            if (isset($data['libelle'])) {
                $salle->setLibelle($data['libelle']);
            }

            if (isset($data['nbPlace'])) {
                $salle->setNbPlace((int)$data['nbPlace']);
            }

            if (isset($data['equipementInfo'])) {
                $salle->setEquipementInfo($data['equipementInfo']);
            }

            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'message' => 'Salle mise à jour avec succès',
            'salle' => $this->serializer->serialize($salle, 'json', ['groups' => 'salle'])
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/salles/{salleId}",
     *     tags={"Salles"},
     *     summary="Supprimer une salle par ID",
     *     @OA\Parameter(
     *         name="salleId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de la salle à supprimer"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Salle supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Salle non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     * @Rest\Delete("/api/salles/{salleId}")
     * @Security(name="Bearer")
     */
    public function deleteSalle(int $salleId): JsonResponse
    {
        $salle = $this->em->getRepository(Salle::class)->find($salleId);

        if (!$salle) {
            return new JsonResponse(['message' => 'Salle non trouvé'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->em->remove($salle);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Salle supprimé avec succès'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Patch(
     *     path="/api/salles/{salleId}/changement-batiment/{batimentId}",
     *     tags={"Salles"},
     *     summary="Changer une salle de bâtiment",
     *     @OA\Parameter(
     *         name="salleId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de la salle"
     *     ),
     *     @OA\Parameter(
     *         name="batimentId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID du bâtiment auquel affecter la salle"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Salle déplacée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bâtiment ou salle non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Patch("/api/salles/{salleId}/changement-batiment/{batimentId}")
     * @Security(name="Bearer")
     */
    public function patchSalleBatiment(int $batimentId, int $salleId): JsonResponse
    {
        try {
            $batiment = $this->em->getRepository(Batiment::class)->find($batimentId);
            $salle = $this->em->getRepository(Salle::class)->find($salleId);

            if (!$batiment || !$salle) {
                return new JsonResponse(['message' => 'Bâtiment ou salle non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $ancienBatiment = $salle->getBatiment();
            $ancienBatiment->removeSalle($salle);
            $salle->setBatiment($batiment);
            $batiment->addSalle($salle);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Salle affectée au bâtiment avec succès'], Response::HTTP_OK);
    }
}
