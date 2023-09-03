<?php

namespace App\Entity;

use App\Repository\ModuleFormationUtilisateurRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleFormationUtilisateurRepository::class)]
class ModuleFormationUtilisateur
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ModuleFormation::class, inversedBy: 'moduleFormationUtilisateurs')]
    private ?ModuleFormation $moduleFormation = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'listeModulesFormations')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $dateDerniereSession = null;

    /**
     * Indique si le prof à les compétences d'enseigner le module.
     * Si {@see $dateDerniereSession} > 6 mois, on doit passer à FALSE.
     */
    #[ORM\Column]
    private ?bool $competenceActive = true;

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

    public function getDateDerniereSession(): ?DateTimeInterface
    {
        return $this->dateDerniereSession;
    }

    public function setDateDerniereSession(DateTimeInterface $dateDerniereSession): self
    {
        $this->dateDerniereSession = $dateDerniereSession;

        return $this;
    }

    public function isCompetenceActive(): ?bool
    {
        return $this->competenceActive;
    }

    public function setCompetenceActive(bool $competenceActive): self
    {
        $this->competenceActive = $competenceActive;

        return $this;
    }
}
