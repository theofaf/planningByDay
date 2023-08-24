<?php

namespace App\Entity;

use App\Repository\ModuleFormationRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
    private ?DateTimeInterface $duree = null;

    #[ORM\ManyToMany(targetEntity: Cursus::class, inversedBy: 'moduleFormations')]
    /** @var ArrayCollection $listeCursus */
    private $listeCursus;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, inversedBy: 'moduleFormations')]
    /** @var ArrayCollection $listeCursus */
    private $listeUtilisateurs;

    public function __construct()
    {
        $this->listeCursus = new ArrayCollection();
        $this->listeUtilisateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDuree(): ?DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(DateTimeInterface $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getListeCursus(): ?ArrayCollection
    {
        return $this->listeCursus;
    }

    public function addCursus(Cursus $cursus): self
    {
        if (!$this->listeCursus->contains($cursus)) {
            $this->listeCursus->add($cursus);
            $cursus->addModuleFormation($this);
        }

        return $this;
    }

    public function setListeCursus(?array $listeCursus): self
    {
        $this->listeCursus = new ArrayCollection();
        /** @var Cursus $cursus */
        foreach ($listeCursus as $cursus) {
            if (!$this->listeCursus->contains($cursus)) {
                $this->listeCursus->add($cursus);
                $cursus->addModuleFormation($this);
            }
        }

        return $this;
    }

    public function removeCursus(Cursus $cursus): self
    {
        $this->listeCursus->removeElement($cursus);

        return $this;
    }

    public function getListeUtilisateurs(): ?ArrayCollection
    {
        return $this->listeUtilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->listeUtilisateurs->contains($utilisateur)) {
            $this->listeUtilisateurs->add($utilisateur);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        $this->listeUtilisateurs->removeElement($utilisateur);

        return $this;
    }
}
