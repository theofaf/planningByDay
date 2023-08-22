<?php

namespace App\Entity;

use App\Repository\ModuleFormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleFormationRepository::class)]
class ModuleFormation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $duree = null;

    #[ORM\ManyToMany(targetEntity: Cursus::class, inversedBy: 'moduleFormations')]
    private Collection $idModuleFormation_Cursus;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, inversedBy: 'moduleFormations')]
    private Collection $idModuleFormation_Utilisateur;

    public function __construct()
    {
        $this->idModuleFormation_Cursus = new ArrayCollection();
        $this->idModuleFormation_Utilisateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(\DateTimeInterface $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * @return Collection<int, Cursus>
     */
    public function getIdModuleFormationCursus(): Collection
    {
        return $this->idModuleFormation_Cursus;
    }

    public function addIdModuleFormationCursu(Cursus $idModuleFormationCursu): static
    {
        if (!$this->idModuleFormation_Cursus->contains($idModuleFormationCursu)) {
            $this->idModuleFormation_Cursus->add($idModuleFormationCursu);
        }

        return $this;
    }

    public function removeIdModuleFormationCursu(Cursus $idModuleFormationCursu): static
    {
        $this->idModuleFormation_Cursus->removeElement($idModuleFormationCursu);

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getIdModuleFormationUtilisateur(): Collection
    {
        return $this->idModuleFormation_Utilisateur;
    }

    public function addIdModuleFormationUtilisateur(Utilisateur $idModuleFormationUtilisateur): static
    {
        if (!$this->idModuleFormation_Utilisateur->contains($idModuleFormationUtilisateur)) {
            $this->idModuleFormation_Utilisateur->add($idModuleFormationUtilisateur);
        }

        return $this;
    }

    public function removeIdModuleFormationUtilisateur(Utilisateur $idModuleFormationUtilisateur): static
    {
        $this->idModuleFormation_Utilisateur->removeElement($idModuleFormationUtilisateur);

        return $this;
    }


}
