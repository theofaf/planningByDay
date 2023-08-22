<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Statut $idStatut = null;

    #[ORM\ManyToOne(inversedBy: 'idReceveur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $idReceveur = null;

    #[ORM\ManyToOne(inversedBy: 'idEmetteur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $idEmetteur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getIdStatut(): ?Statut
    {
        return $this->idStatut;
    }

    public function setIdStatut(?Statut $idStatut): static
    {
        $this->idStatut = $idStatut;

        return $this;
    }

    public function getIdReceveur(): ?Utilisateur
    {
        return $this->idReceveur;
    }

    public function setIdReceveur(?Utilisateur $idReceveur): static
    {
        $this->idReceveur = $idReceveur;

        return $this;
    }

    public function getIdEmetteur(): ?Utilisateur
    {
        return $this->idEmetteur;
    }

    public function setIdEmetteur(?Utilisateur $idEmetteur): static
    {
        $this->idEmetteur = $idEmetteur;

        return $this;
    }
}
