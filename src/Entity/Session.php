<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["nelmio", "session", "utilisateur", "classe", "salle"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["nelmio", "session"])]
    private ?DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["nelmio", "session"])]
    private ?DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "session"])]
    private ?ModuleFormation $moduleFormation = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "session"])]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "session"])]
    private ?Classe $classe = null;

    #[ORM\ManyToOne(targetEntity: Salle::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "session"])]
    private ?Salle $salle = null;

    #[ORM\ManyToOne(targetEntity: Statut::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "session"])]
    private ?Statut $statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getModuleFormation(): ?ModuleFormation
    {
        return $this->moduleFormation;
    }

    public function setModuleFormation(?ModuleFormation $moduleFormation): self
    {
        $this->moduleFormation = $moduleFormation;

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

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): self
    {
        $this->salle = $salle;

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
}
