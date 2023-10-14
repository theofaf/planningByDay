<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * Récupère les messages reçus par un utilisateur spécifié et les marque comme lus.
     *
     * @Route("/messages-recus/{id}", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/messages-recus/{id}",
     *     summary="Récupère les messages reçus par un utilisateur spécifié et les marque comme lus.",
     *     @SWG\Response(
     *         response=200,
     *         description="Messages reçus",
     *         @Model(type=Message::class, groups={"read"})
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Utilisateur non trouvé."
     *     )
     * )
     */
    public function messagesRecus(int $id): JsonResponse
    {
        
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->find($id);

       
        if (!$utilisateur) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        
        $messages = $this->entityManager->getRepository(Message::class)->findBy(['receveur' => $utilisateur]);

        
        foreach ($messages as $message) {
            $message->getStatut()->setLibelle('lu'); 
        }

        
        $this->entityManager->flush();

        
        return new JsonResponse($messages, JsonResponse::HTTP_OK);
    }
    /**
     * Envoie un message à un utilisateur spécifié et le marque comme non lu.
     *
     * @Route("/envoyer-message", methods={"POST"})
     *
     * @SWG\Post(
     *     path="/envoyer-message",
     *     summary="Envoie un message à un utilisateur spécifié et le marque comme non lu.",
     *     @SWG\Parameter(
     *         name="Contenu",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="Emetteur (ID)",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Parameter(
     *         name="Receveur (ID)",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Message envoyé avec succès.",
     *         @Model(type=Message::class, groups={"read"})
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Requête invalide."
     *     )
     * )
     */
    public function envoyerMessage(Request $request): JsonResponse
    {
       
        $data = json_decode($request->getContent(), true);

        
        $message = new Message();
        $message->setContenu($data['contenu']);

        
        $statutNonLu = $this->entityManager->getRepository(Statut::class)->findOneBy(['libelle' => 'non lu']);

        
        if (!$statutNonLu) {
            return new JsonResponse(['message' => 'Statut "non lu" non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

       
        $message->setStatut($statutNonLu);

        
        $emetteur = $this->entityManager->getRepository(Utilisateur::class)->find($data['emetteur']);
        $receveur = $this->entityManager->getRepository(Utilisateur::class)->find($data['receveur']);

        
        if (!$emetteur || !$receveur) {
            return new JsonResponse(['message' => 'Emetteur ou Receveur non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

       
        $message->setEmetteur($emetteur);
        $message->setReceveur($receveur);

        
        $message->setDateEnvoi(new \DateTime());

        
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        
        return new JsonResponse(['message' => 'Message envoyé avec succès', 'message' => $message->toArray()], JsonResponse::HTTP_CREATED);
    }
    /**
     * Récupère tous les messages reçus par un utilisateur, regroupés par expéditeur.
     *
     * @Route("/messages-recus/groupes/{id}", methods={"GET"})
     *
     * @SWG\Get(
     *     path="/messages-recus/groupes/{id}",
     *     summary="Récupère tous les messages reçus par un utilisateur, regroupés par expéditeur.",
     *     @SWG\Response(
     *         response=200,
     *         description="Messages reçus, groupés par expéditeur",
     *         @Model(type=Message::class, groups={"read"})
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Utilisateur non trouvé."
     *     )
     * )
     */
    public function messagesRecusGroupes(int $id): JsonResponse
    {
        
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->find($id);

        
        if (!$utilisateur) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        
        $messages = $this->entityManager->getRepository(Message::class)->findBy(['receveur' => $utilisateur]);

        
        $messagesGroupes = [];

        foreach ($messages as $message) {
            $expediteurId = $message->getEmetteur()->getId();

            
            if (!array_key_exists($expediteurId, $messagesGroupes)) {
                $messagesGroupes[$expediteurId] = [
                    'expediteur' => $message->getEmetteur(),
                    'messages' => [],
                ];
            }

            
            $messagesGroupes[$expediteurId]['messages'][] = $message;
        }

        
        return new JsonResponse(array_values($messagesGroupes), JsonResponse::HTTP_OK);
    }
}


