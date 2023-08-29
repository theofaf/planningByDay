<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Statut $statut = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'messagesRecues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $receveur = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'messagesEnvoyees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $emetteur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $dateEnvoi = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

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

    public function getReceveur(): ?Utilisateur
    {
        return $this->receveur;
    }

    public function setReceveur(?Utilisateur $receveur): self
    {
        $this->receveur = $receveur;

        return $this;
    }

    public function getEmetteur(): ?Utilisateur
    {
        return $this->emetteur;
    }

    public function setEmetteur(?Utilisateur $emetteur): self
    {
        $this->emetteur = $emetteur;

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
