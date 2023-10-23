<?php

namespace App\Controller;


use App\Entity\Cursus;
use App\Entity\ModuleFormationUtilisateur;
use App\Entity\Session;
use App\Entity\Utilisateur;
use App\Service\LogService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Exception;
use App\Entity\ModuleFormation;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ModuleController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
        private readonly LogService $logService,
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/modules",
     *     tags={"Modules"},
     *     summary="Créer un nouveau module",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du module à créer",
     *         @OA\JsonContent(ref=@Model(type=ModuleFormation::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Module créé avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=ModuleFormation::class, groups={"moduleFormation"}))
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
     * @Rest\Post("/api/modules")
     * @Security(name="Bearer")
     */
    public function postModule(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            null === $data
            || !isset($data['libelle'])
            || (!isset($data['duree']) || $data['duree'] > 23)
        ) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $module = (new ModuleFormation())
                ->setLibelle($data['libelle'])
                ->setDuree(DateTime::createFromFormat('H', $data['duree']));

            $this->em->persist($module);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La création du module a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $moduleSerialize = $this->serializer->serialize($module, 'json', ['groups' => 'moduleFormation']);
        return new JsonResponse(['message' => 'Module créé avec succès', 'module' => $moduleSerialize], Response::HTTP_CREATED);
    }


    /**
     * @OA\Put(
     *     path="/api/modules/{moduleId}",
     *     tags={"Modules"},
     *     summary="Modifier un module par son ID",
     *     @OA\Parameter(
     *           name="moduleId",
     *           @OA\Schema(type="integer"),
     *           in="path",
     *           required=true,
     *           description="ID du module à modifier"
     *       ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du module à modifier",
     *         @OA\JsonContent(ref=@Model(type=ModuleFormation::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Module modifié avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=ModuleFormation::class, groups={"moduleFormation"}))
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
     * @Rest\Put("/api/modules/{moduleId}")
     * @Security(name="Bearer")
     */
    public function putModule(int $moduleId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            null === $data
            || !isset($data['libelle'])
            || (!isset($data['duree']) || !is_int($data['duree']) || $data['duree'] > 23)
        ) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $module = $this->em->getRepository(ModuleFormation::class)->find($moduleId);
            $module
                ->setLibelle($data['libelle'])
                ->setDuree(DateTime::createFromFormat('H', $data['duree']));
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La modification du module a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $moduleSerialize = $this->serializer->serialize($module, 'json', ['groups' => 'moduleFormation']);
        return new JsonResponse(['message' => 'Module modifié avec succès', 'module' => $moduleSerialize], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/modules/{moduleId}",
     *     tags={"Modules"},
     *     summary="Supprimer un module par ID",
     *     @OA\Parameter(
     *         name="moduleId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID du module à supprimer"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Module supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Module non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     * @Rest\Delete("/api/modules/{moduleId}")
     * @Security(name="Bearer")
     */
    public function deleteModule(int $moduleId): JsonResponse
    {
        try {
            $module = $this->em->getRepository(ModuleFormation::class)->find($moduleId);
            if (!$module) {
                return new JsonResponse(['message' => 'Module non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $sessions = $this->em->getRepository(Session::class)->findBy(['moduleFormation' => $module->getId()]);
            foreach ($sessions as $session) {
                $this->em->remove($session);
            }
            $this->em->remove($module);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La suppression du module [$moduleId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Module supprimé avec succès'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *     path="/api/modules",
     *     tags={"Modules"},
     *     summary="Récupère tous les modules",
     *     @OA\Response(
     *          response=200,
     *          description="Tous les modules sont retournés",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=ModuleFormation::class, groups={"moduleFormation"}))
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/modules")
     * @Security(name="Bearer")
     */
    public function getModules(): JsonResponse
    {
        try {
            $modules = $this->em->getRepository(ModuleFormation::class)->findAll();
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des modules a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($modules, 'json', ['groups' => 'moduleFormation']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/modules/cursus/{cursusId}",
     *     tags={"Modules"},
     *     summary="Récupère les modules d'un cursus",
     *     @OA\Parameter(
     *          name="cursusId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID du cursus"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="La liste des modules d'un cursus est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=ModuleFormation::class, groups={"moduleFormation"}))
     *           )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Le cursus n'a pas été trouvé"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/modules/cursus/{cursusId}")
     * @Security(name="Bearer")
     */
    public function getModulesParCursusId(int $cursusId): JsonResponse
    {
        try {
            $cursus = $this->em->getRepository(Cursus::class)->find($cursusId);

            if (!$cursus) {
                return new JsonResponse(['message' => "Le cursus n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $modules = $this->em->getRepository(ModuleFormation::class)->getModulesParCursusId($cursus->getId());
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des modules du cursus [$cursusId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($modules, 'json', ['groups' => 'moduleFormation']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/modules/utilisateurs/{utilisateurId}",
     *     tags={"Modules"},
     *     summary="Récupère les modules d'un utilisateur",
     *     @OA\Parameter(
     *          name="utilisateurId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de l'utilisateur"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="La liste des modules d'un utilisateur est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=ModuleFormation::class, groups={"moduleFormation"}))
     *           )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="L'utilisateur n'a pas été trouvé"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/modules/utilisateurs/{utilisateurId}")
     * @Security(name="Bearer")
     */
    public function getModulesParUtilisateurId(int $utilisateurId): JsonResponse
    {
        try {
            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);

            if (!$utilisateur) {
                return new JsonResponse(['message' => "L'utilisateur n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $modules = $this->em->getRepository(ModuleFormationUtilisateur::class)->findBy(['utilisateur' => $utilisateur->getId()]);
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des modules de l'utilisateur [$utilisateurId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($modules, 'json', ['groups' => 'ModuleFormationUtilisateur']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/modules/{moduleId}",
     *     tags={"Modules"},
     *     summary="Récupère un module par son ID",
     *     @OA\Parameter(
     *          name="moduleId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID du module"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="Le module est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=ModuleFormation::class, groups={"moduleFormation"}))
     *           )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Le module n'a pas été trouvé"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/modules/{moduleId}")
     * @Security(name="Bearer")
     */
    public function getModulesParId(int $moduleId): JsonResponse
    {
        try {
            $module = $this->em->getRepository(ModuleFormation::class)->find($moduleId);

            if (!$module) {
                return new JsonResponse(['message' => "Le module n'existe pas"], Response::HTTP_NOT_FOUND);
            }
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération du module [$moduleId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($module, 'json', ['groups' => 'moduleFormation']), Response::HTTP_OK);
    }
}
