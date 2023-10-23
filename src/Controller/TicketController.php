<?php

namespace App\Controller;

use App\Entity\Etablissement;
use App\Entity\Statut;
use App\Entity\Utilisateur;
use App\Service\LogService;
use DateTime;
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
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Ticket;

class TicketController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
        private readonly LogService $logService,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/tickets",
     *     tags={"Tickets"},
     *     summary="Récupère les tickets",
     *     @OA\Response(
     *          response=200,
     *          description="Les tickets sont retournés",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Ticket::class, groups={"ticket"}))
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/tickets")
     * @Security(name="Bearer")
     */
    public function getTickets(): JsonResponse
    {
        try {
            $tickets = $this->em->getRepository(Ticket::class)->findAll();
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des tickets a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $ticketSerialize = $this->serializer->serialize($tickets, 'json', ['groups' => 'ticket']);
        return new JsonResponse($ticketSerialize, Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/tickets/{ticketId}",
     *     tags={"Tickets"},
     *     summary="Récupère un ticket par ID",
     *     @OA\Parameter(
     *          name="ticketId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID du ticket"
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Le ticket est retourné",
     *          @OA\JsonContent(
     *                type="array",
     *                @OA\Items(ref=@Model(type=Ticket::class, groups={"ticket"}))
     *            )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur technique"
     *     )
     * )
     *
     * @Rest\Get("/api/tickets/{ticketId}")
     * @Security(name="Bearer")
     */
    public function getTicketsParId(int $ticketId): JsonResponse
    {
        try {
            $ticket = $this->em->getRepository(Ticket::class)->find($ticketId);
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération du ticket [$ticketId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $ticketSerialize = $this->serializer->serialize($ticket, 'json', ['groups' => 'ticket']);
        return new JsonResponse($ticketSerialize, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/tickets",
     *     tags={"Tickets"},
     *     summary="Créer un nouveau ticket",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du ticket à créer",
     *         @OA\JsonContent(ref=@Model(type=Ticket::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket créé avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Ticket::class, groups={"ticket"}))
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
     * @Rest\Post("/api/tickets")
     * @Security(name="Bearer")
     */
    public function postTicket(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            null === $data
            || !isset($data['utilisateurId'])
            || !isset($data['etablissementId'])
            || !isset($data['sujet'])
            || !isset($data['message'])
        ) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $statut = $this->em->getRepository(Statut::class)->find(Statut::STATUT_PUBLIE_ID);
            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($data['utilisateurId']);
            $etablissement = $this->em->getRepository(Etablissement::class)->find($data['etablissementId']);

            if (!$statut) {
                return new JsonResponse(['message' => 'Statut non trouvé'], Response::HTTP_NOT_FOUND);
            }

            if (!$utilisateur) {
                return new JsonResponse(['message' => 'utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
            }

            if (!$etablissement) {
                return new JsonResponse(['message' => 'Établissement non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $ticket = (new Ticket())
                ->setSujet($data['sujet'])
                ->setMessage($data['message'])
                ->setStatut($statut)
                ->setUtilisateur($utilisateur)
                ->setEtablissement($etablissement)
                ->setDateEnvoi(new DateTime())
            ;
            $this->em->persist($ticket);
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La création du ticket a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $ticketSerialize = $this->serializer->serialize($ticket, 'json', ['groups' => 'ticket']);
        return new JsonResponse(['message' => 'Abonnement créé avec succès' , 'ticket' => $ticketSerialize], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/tickets/{ticketId}",
     *     tags={"Tickets"},
     *     summary="Mettre à jour les détails d'un ticket par ID",
     *     @OA\Parameter(
     *         name="ticketId",
     *         @OA\Schema(type="integer"),
     *         in="path",
     *         required=true,
     *         description="ID du ticket"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du ticket à mettre à jour",
     *         @OA\JsonContent(ref=@Model(type=Ticket::class, groups={"nelmio"}))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket mis à jour avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Ticket::class, groups={"ticket"}))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket ou statut non trouvé"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Erreur technique"
     *      )
     * )
     *
     * @Rest\Put("/api/tickets/{ticketId}")
     * @Security(name="Bearer")
     */
    public function putTicket(int $ticketId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            null === $data || (
                !isset($data['sujet'])
                && !isset($data['message'])
                && !isset($data['statutId'])
            )
        ) {
            return new JsonResponse(['message' => 'Les données sont invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $ticket = $this->em->getRepository(Ticket::class)->find($ticketId);
            if (!$ticket) {
                return new JsonResponse(['message' => 'Ticket non trouvé'], Response::HTTP_NOT_FOUND);
            }

            if (isset($data['message'])) {
                $ticket->setSujet($data['message']);
            }

            if (isset($data['sujet'])) {
                $ticket->setSujet($data['sujet']);
            }

            if (isset($data['statutId'])) {
                $statut = $this->em->getRepository(Statut::class)->find($data['statutId']);

                if (!$statut) {
                    return new JsonResponse(['message' => 'Statut non trouvé'], Response::HTTP_NOT_FOUND);
                }

                $ticket->setStatut($statut);
            }
            $this->em->flush();
        } catch (Exception $exception) {
            $this->logService->insererLog("La modification du ticket [$ticketId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $ticketSerialize = $this->serializer->serialize($ticket, 'json', ['groups' => 'ticket']);
        return new JsonResponse(['message' => 'Ticket mis à jour avec succès', 'ticket' => $ticketSerialize], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/tickets/etablissement/{etablissementId}",
     *     tags={"Tickets"},
     *     summary="Récupère les tickets d'un établissement",
     *     @OA\Parameter(
     *          name="etablissementId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de l'établissement"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="La liste des tickets d'un établissement est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Ticket::class, groups={"ticket"}))
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
     * @Rest\Get("/api/tickets/etablissement/{etablissementId}")
     * @Security(name="Bearer")
     */
    public function getTicketsByEtablissement(int $etablissementId): JsonResponse
    {
        try {
            $etablissement = $this->em->getRepository(Etablissement::class)->find($etablissementId);

            if (!$etablissement) {
                return new JsonResponse(['message' => "L'établissement n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $tickets = $this->em->getRepository(Ticket::class)->findBy(['etablissement' => $etablissement->getId()]);
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des tickets de l'établissement [$etablissementId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($tickets, 'json', ['groups' => 'ticket']), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/tickets/utilisateur/{utilisateurId}",
     *     tags={"Tickets"},
     *     summary="Récupère les tickets d'un utilisateur",
     *     @OA\Parameter(
     *          name="utilisateurId",
     *          @OA\Schema(type="integer"),
     *          in="path",
     *          required=true,
     *          description="ID de l'utilisateur"
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="La liste des tickets d'un utilisateur est retournée",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(ref=@Model(type=Ticket::class, groups={"ticket"}))
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
     * @Rest\Get("/api/tickets/utilisateur/{utilisateurId}")
     * @Security(name="Bearer")
     */
    public function getTicketsByUtilisateur(int $utilisateurId): JsonResponse
    {
        try {
            $utilisateur = $this->em->getRepository(Utilisateur::class)->find($utilisateurId);

            if (!$utilisateur) {
                return new JsonResponse(['message' => "L'utilisateur n'existe pas"], Response::HTTP_NOT_FOUND);
            }

            $tickets = $this->em->getRepository(Ticket::class)->findBy(['utilisateur' => $utilisateur->getId()]);
        } catch (Exception $exception) {
            $this->logService->insererLog("La récupération des tickets de l'utilisateur [$utilisateurId] a échoué", $exception);
            return new JsonResponse(['message' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($this->serializer->serialize($tickets, 'json', ['groups' => 'ticket']), Response::HTTP_OK);
    }
}
