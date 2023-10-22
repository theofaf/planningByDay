<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Service\ServiceClasse;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use App\Entity\Classe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;

class ClasseController extends AbstractController
{
    public function __construct(
        private readonly ServiceClasse $serviceClasse,
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/classes",
     *     tags={"Classes"},
     *     summary="Créer une nouvelle classe",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de la classe à créer",
     *         @OA\JsonContent(ref=@Model(type=Classe::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Classe créée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Classe::class, groups={"classe"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Cursus non trouvé"
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Post("/api/classes")
     * @Security(name="Bearer")
     */
    public function postClasse(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$this->serviceClasse->isDataValide($data)) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        $classe = (new Classe())
            ->setLibelle($data['libelle'])
            ->setNombreEleves($data['nombreEleves'])
        ;

        try {
            $cursus = $this->em->getRepository(Cursus::class)->find($data['cursusId']);
            if (!$cursus) {
                return new JsonResponse(['message' => 'Cursus non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $classe->setCursus($cursus);
            $this->em->persist($classe);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $batimentSerealize = $this->serializer->serialize($classe, 'json', ['groups' => 'classe']);
        return new JsonResponse(['message' => 'Classe créé avec succès', 'batiment' => $batimentSerealize], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/classes/{classeId}",
     *     tags={"Classes"},
     *     summary="Récupère une classe par son ID",
     *     @OA\Parameter(
     *          name="classeId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de la classe"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="La classe est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Classe::class, groups={"classe"}))
     *           )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="La classe n'a pas été trouvée"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/classes/{classeId}")
     * @Security(name="Bearer")
     */
    public function getClasseParId(int $classeId): JsonResponse
    {
        try {
            $classe = $this->em->getRepository(Classe::class)->find($classeId);

            if (!$classe) {
                return new JsonResponse(['message' => 'Classe non trouvée'], Response::HTTP_NOT_FOUND);
            }
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($classe, 'json', ['groups' => 'classe']), Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/classes/{classeId}",
     *     tags={"Classes"},
     *     summary="Modifier une classe",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de la classe à modifier",
     *         @OA\JsonContent(ref=@Model(type=Classe::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Classe modifiée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Classe::class, groups={"classe"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Cursus non trouvé"
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Put("/api/classes/{classeId}")
     * @Security(name="Bearer")
     */
    public function putClasse(int $classeId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            if (!$this->serviceClasse->isDataValide($data)) {
                return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
            }

            $classe = $this->em->getRepository(Classe::class)->find($classeId);
            $cursus = $this->em->getRepository(Cursus::class)->find($data['cursusId']);

            if (!$classe || !$cursus) {
                return new JsonResponse(['message' => 'Classe ou cursus non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $classe
                ->setLibelle($data['libelle'])
                ->setNombreEleves($data['nombreEleves'])
                ->setCursus($cursus)
            ;
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->em->flush();

        $classeSerealize = $this->serializer->serialize($classe, 'json', ['groups' => 'classe']);
        return new JsonResponse(['message' => 'Classe mis à jour avec succès', 'classe' => $classeSerealize], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/classes/{classeId}",
     *     tags={"Classes"},
     *     summary="Supprimer une classe par ID",
     *     @OA\Parameter(
     *         name="classeId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID de la classe à supprimer"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Classe supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Classe non trouvée"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     * @Rest\Delete("/api/classes/{classeId}")
     * @Security(name="Bearer")
     */
    public function deleteClasse(int $classeId): JsonResponse
    {
        $classe = $this->em->getRepository(Classe::class)->find($classeId);

        if (!$classe) {
            return new JsonResponse(['message' => 'Classe non trouvée'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->em->remove($classe);
            $this->em->flush();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Classe supprimée avec succès'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *     path="/api/classes",
     *     tags={"Classes"},
     *     summary="Récupère toutes les classes",
     *     @OA\Response(
     *          response=200,
     *          description="Tous les classes sont retournées",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Classe::class, groups={"classe"}))
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/classes")
     * @Security(name="Bearer")
     */
    public function getClasses(): JsonResponse
    {
        try {
            $classes = $this->em->getRepository(Classe::class)->findAll();
        } catch (Exception) {
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($classes, 'json', ['groups' => 'classe']), Response::HTTP_OK);
    }
}
