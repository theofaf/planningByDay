<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["nelmio", "ticket", "etablissement", "utilisateur"])]
    private ?int $id = null;

    #[ORM\Column(length: 35)]
    #[Groups(["nelmio", "ticket"])]
    private ?string $sujet = null;

    #[ORM\Column(length: 255)]
    #[Groups(["nelmio", "ticket"])]
    private ?string $message = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "ticket"])]
    private ?Statut $statut = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "ticket"])]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Etablissement::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "ticket"])]
    private ?Etablissement $etablissement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["nelmio", "ticket"])]
    private ?DateTimeInterface $dateEnvoi = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getStatut(): ?Statut
    {
        return $this->statut;
    }

    public function setStatut(?Statut $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getEtablissement(): ?Etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?Etablissement $etablissement): self
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    public function getDateEnvoi(): ?DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(?DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;
        return $this;
    }
}
