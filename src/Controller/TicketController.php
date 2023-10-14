<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    /**
     * Créer un nouveau ticket.
     *
     * @Route("/tickets", methods={"POST"})
     *
     * @SWG\Post(
     *     path="/tickets",
     *     summary="Créer un nouveau ticket.",
     *     @SWG\Parameter(
     *         name="sujet",
     *         in="body",
     *         @SWG\Schema(type="string"),
     *         description="Sujet du ticket"
     *     ),
     *     @SWG\Parameter(
     *         name="message",
     *         in="body",
     *         @SWG\Schema(type="string"),
     *         description="Message du ticket"
     *     ),
     *     @SWG\Parameter(
     *         name="statut_id",
     *         in="body",
     *         @SWG\Schema(type="integer"),
     *         description="ID du statut du ticket"
     *     ),
     *     @SWG\Parameter(
     *         name="utilisateur_id",
     *         in="body",
     *         @SWG\Schema(type="integer"),
     *         description="ID de l'utilisateur du ticket"
     *     ),
     *     @SWG\Parameter(
     *         name="etablissement_id",
     *         in="body",
     *         @SWG\Schema(type="integer"),
     *         description="ID de l'établissement du ticket"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Ticket créé avec succès"
     *     )
     * )
     */
    public function createTicket(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $statutRepository = $entityManager->getRepository(Statut::class);
        $utilisateurRepository = $entityManager->getRepository(Utilisateur::class);
        $etablissementRepository = $entityManager->getRepository(Etablissement::class);

        $statut = $statutRepository->find($data['statut_id']);
        $utilisateur = $utilisateurRepository->find($data['utilisateur_id']);
        $etablissement = $etablissementRepository->find($data['etablissement_id']);

        if (!$statut || !$utilisateur || !$etablissement) {
            return new JsonResponse(['message' => 'Statut, utilisateur ou établissement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $ticket = new Ticket();
        $ticket->setSujet($data['sujet']);
        $ticket->setMessage($data['message']);
        $ticket->setStatut($statut);
        $ticket->setUtilisateur($utilisateur);
        $ticket->setEtablissement($etablissement);

        $entityManager->persist($ticket);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Ticket créé avec succès', 'ticket' => $ticket->toArray()], JsonResponse::HTTP_CREATED);
    }
    /**
     * Mettre à jour un ticket par ID.
     *
     * @Route("/tickets/{id}", methods={"PUT"})
     *
     * @SWG\Put(
     *     path="/tickets/{id}",
     *     summary="Mettre à jour un ticket par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID du ticket"
     *     ),
     *     @SWG\Parameter(
     *         name="sujet",
     *         in="body",
     *         @SWG\Schema(type="string"),
     *         description="Nouveau sujet du ticket"
     *     ),
     *     @SWG\Parameter(
     *         name="message",
     *         in="body",
     *         @SWG\Schema(type="string"),
     *         description="Nouveau message du ticket"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Ticket mis à jour avec succès"
     *     )
     * )
     */
    public function updateTicket(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $ticketRepository = $entityManager->getRepository(Ticket::class);
        $ticket = $ticketRepository->find($id);

        if (!$ticket) {
            return new JsonResponse(['message' => 'Ticket non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $ticket->setSujet($data['sujet']);
        $ticket->setMessage($data['message']);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Ticket mis à jour avec succès', 'ticket' => $ticket->toArray()], JsonResponse::HTTP_OK);
    }
    /**
     * Récupérer un ticket par ID.
     *
     * @Route("/tickets/{id}", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/tickets/{id}",
     *     summary="Récupérer un ticket par ID.",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="ID du ticket à récupérer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Ticket récupéré avec succès"
     *     )
     * )
     */
    public function getTicket(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ticketRepository = $entityManager->getRepository(Ticket::class);
        $ticket = $ticketRepository->find($id);

        if (!$ticket) {
            return new JsonResponse(['message' => 'Ticket non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($ticket->toArray(), JsonResponse::HTTP_OK);
    }
    /**
     * Récupérer tous les tickets.
     *
     * @Route("/tickets", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/tickets",
     *     summary="Récupérer tous les tickets.",
     *     @SWG\Response(
     *         response=200,
     *         description="Tickets récupérés avec succès"
     *     )
     * )
     */
    public function getAllTickets(): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ticketRepository = $entityManager->getRepository(Ticket::class);
        $tickets = $ticketRepository->findAll();

        $ticketsData = [];

        foreach ($tickets as $ticket) {
            $ticketsData[] = $ticket->toArray();
        }

        return new JsonResponse($ticketsData, JsonResponse::HTTP_OK);
    }
    /**
     * Récupérer tous les tickets faits par un établissement.
     *
     * @Route("/etablissements/{etablissementId}/tickets", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/etablissements/{etablissementId}/tickets",
     *     summary="Récupérer tous les tickets faits par un établissement.",
     *     @SWG\Parameter(
     *         name="etablissementId",
     *         in="path",
     *         type="integer",
     *         description="ID de l'établissement"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Tickets récupérés avec succès"
     *     )
     * )
     */
    public function getTicketsByEtablissement(int $etablissementId): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $etablissementRepository = $entityManager->getRepository(Etablissement::class);
        $etablissement = $etablissementRepository->find($etablissementId);

        if (!$etablissement) {
            return new JsonResponse(['message' => 'Établissement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $ticketRepository = $entityManager->getRepository(Ticket::class);
        $tickets = $ticketRepository->findBy(['etablissement' => $etablissement]);

        $ticketsData = [];

        foreach ($tickets as $ticket) {
            $ticketsData[] = $ticket->toArray();
        }

        return new JsonResponse($ticketsData, JsonResponse::HTTP_OK);
    }
    /**
     * Récupérer un ticket fait par un établissement.
     *
     * @Route("/etablissements/{etablissementId}/tickets/{ticketId}", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/etablissements/{etablissementId}/tickets/{ticketId}",
     *     summary="Récupérer un ticket fait par un établissement.",
     *     @SWG\Parameter(
     *         name="etablissementId",
     *         in="path",
     *         type="integer",
     *         description="ID de l'établissement"
     *     ),
     *     @SWG\Parameter(
     *         name="ticketId",
     *         in="path",
     *         type="integer",
     *         description="ID du ticket à récupérer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Ticket récupéré avec succès"
     *     )
     * )
     */
    public function getTicketByEtablissement(int $etablissementId, int $ticketId): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $etablissementRepository = $entityManager->getRepository(Etablissement::class);
        $etablissement = $etablissementRepository->find($etablissementId);

        if (!$etablissement) {
            return new JsonResponse(['message' => 'Établissement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $ticketRepository = $entityManager->getRepository(Ticket::class);
        $ticket = $ticketRepository->findOneBy(['etablissement' => $etablissement, 'id' => $ticketId]);

        if (!$ticket) {
            return new JsonResponse(['message' => 'Ticket non trouvé pour cet établissement'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($ticket->toArray(), JsonResponse::HTTP_OK);
    }
}
