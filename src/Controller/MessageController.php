<?php

namespace App\Controller;

use App\Entity\Statut;
use App\Entity\Utilisateur;
use App\Service\LogService;
use App\Service\MessageService;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Message;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class MessageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
        private readonly MessageService $serviceMessage,
        private readonly LogService $logService,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/messages/{utilisateurId}/recus",
     *     tags={"Messages"},
     *     summary="Récupère les messages reçus pour un utilisateur",
     *     @OA\Parameter(
     *          name="utilisateurId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID du receveur des messages"
     *      ),
     *     @OA\Parameter(
     *           name="isGrouperParEmetteur",
     *           @OA\Schema(type="boolean"),
     *           in="query",
     *           required=false,
     *           description="Booléen (0,1) indiquant si on doit grouper les messages reçus par émetteur"
     *       ),
     *     @OA\Response(
     *           response=200,
     *           description="Les messages reçus sont retournés",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Message::class, groups={"message"}))
     *           )
     *      ),
     *     @OA\Response(
     *          response=404,
     *          description="L'utilisateur n'a pas été trouvé"
     *      ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/messages/{utilisateurId}/recus")
     * @Security(name="Bearer")
     */
    public function getMessagesRecus(int $utilisateurId, ?Request $request): JsonResponse
    {
        $isGrouperParEmettteur = boolval($request?->query?->get('isGrouperParEmetteur'));

        try {
            $receveur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);

            if (!$receveur) {
                return new JsonResponse(['message' => "L'utilisateur n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $messagesRecus = $this->em->getRepository(Message::class)->findBy(['receveur' => $receveur->getId()]);

            if ($isGrouperParEmettteur) {
                $listeMessagesGroupes = [];

                foreach ($messagesRecus as $unMessage) {
                    $emetteurId = $unMessage->getEmetteur()->getId();
                    if (!isset($listeMessagesGroupes[$emetteurId])) {
                        $listeMessagesGroupes[$emetteurId] = [];
                    }

                    $listeMessagesGroupes[$emetteurId][] = $this->serializer->serialize($unMessage, 'json', ['groups' => 'message']);
                }

                return $this->json($listeMessagesGroupes, Response::HTTP_OK);
            }

        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des messages reçus pour l'utilisateur [$utilisateurId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($messagesRecus, 'json', ['groups' => 'message']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/messages/{utilisateurId}/envoyes",
     *     tags={"Messages"},
     *     summary="Récupère les messages envoyés d'un utilisateur",
     *     @OA\Parameter(
     *          name="utilisateurId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de l'émetteur des messages"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="Les messages envoyés sont retournés",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Message::class, groups={"message"}))
     *           )
     *      ),
     *     @OA\Response(
     *          response=404,
     *          description="L'utilisateur n'a pas été trouvé"
     *      ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/messages/{utilisateurId}/envoyes")
     * @Security(name="Bearer")
     */
    public function getMessagesEnvoyes(int $utilisateurId): JsonResponse
    {
        try {
            $receveur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);

            if (!$receveur) {
                return new JsonResponse(['message' => "L'utilisateur n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $messagesEnvoyes = $this->em->getRepository(Message::class)->findBy(['emetteur' => $receveur->getId()]);
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des messages envoyés pour l'utilisateur [$utilisateurId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($messagesEnvoyes, 'json', ['groups' => 'message']), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/messages",
     *     tags={"Messages"},
     *     summary="Créer un nouveau message",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du message à créer",
     *         @OA\JsonContent(ref=@Model(type=Message::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message créé avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Message::class, groups={"message"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Emetteur ou receveur non trouvé"
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Post("/api/messages")
     * @Security(name="Bearer")
     */
    public function postMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$this->serviceMessage->isDataValide($data)) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $message = (new Message())->setContenu($data['contenu']);
            $receveur = $this->em->getRepository(Utilisateur::class)->find($data['receveurId']);
            $emetteur = $this->em->getRepository(Utilisateur::class)->find($data['emetteurId']);
            $statut = $this->em->getRepository(Statut::class)->find(Statut::STATUT_PUBLIE_ID);
            if (!$receveur || !$emetteur) {
                return new JsonResponse(['message' => 'Émetteur ou receveur non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $message
                ->setReceveur($receveur)
                ->setEmetteur($emetteur)
                ->setStatut($statut)
                ->setDateEnvoi(new DateTime())
            ;
            $this->em->persist($message);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La création du message a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageSerealize = $this->serializer->serialize($message, 'json', ['groups' => 'message']);
        return new JsonResponse(['message' => 'Message créé avec succès', $messageSerealize], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/messages/{messageId}",
     *     tags={"Messages"},
     *     summary="Changer la visibilité d'un message",
     *     description="Cette action permet de passer un message à lu/non_lu",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du message à modifier",
     *         @OA\JsonContent(ref=@Model(type=Message::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message modifié avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Message::class, groups={"message"}))
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
     * @Rest\Put("/api/messages/{messageId}")
     * @Security(name="Bearer")
     */
    public function putMessage(int $messageId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['estLu'])) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $message = $this->em->getRepository(Message::class)->find($messageId);
            if (!$message) {
                return new JsonResponse(['message' => 'Message non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $message->setEstLu(boolval($data['estLu']));
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La modification du message [$messageId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageSerealize = $this->serializer->serialize($message, 'json', ['groups' => 'message']);
        return new JsonResponse(['message' => 'Message modifié avec succès', $messageSerealize], Response::HTTP_CREATED);
    }
}


