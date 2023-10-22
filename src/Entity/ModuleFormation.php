<?php

namespace App\Entity;

use App\Repository\ModuleFormationRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ModuleFormationRepository::class)]
class ModuleFormation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["nelmio", "moduleFormation", "session", "ModuleFormationUtilisateur"])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups(["nelmio", "moduleFormation", "ModuleFormationUtilisateur"])]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(["nelmio", "moduleFormation", "ModuleFormationUtilisateur"])]
    private ?DateTimeInterface $duree = null;

    #[ORM\ManyToMany(targetEntity: Cursus::class, inversedBy: 'moduleFormations')]
    #[Groups(["nelmio", "moduleFormation"])]
    /** @var ArrayCollection $listeCursus */
    private $listeCursus;

    #[ORM\OneToMany(mappedBy: 'moduleFormation', targetEntity: ModuleFormationUtilisateur::class, cascade: ['persist'])]
    #[Groups(["nelmio"])]
    /** @var ArrayCollection $moduleFormationUtilisateurs */
    private $moduleFormationUtilisateurs;

    public function __construct()
    {
        $this->listeCursus = new ArrayCollection();
        $this->moduleFormationUtilisateurs = new ArrayCollection();
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

    /** @return ArrayCollection<Cursus> */
    public function getListeCursus()
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

    /** @return ArrayCollection<ModuleFormationUtilisateur> */
    public function getModuleFormationUtilisateurs()
    {
        return $this->moduleFormationUtilisateurs;
    }

    public function addModuleFormationUtilisateur(ModuleFormationUtilisateur $moduleFormationUtilisateur): self
    {
        if (!$this->moduleFormationUtilisateurs->contains($moduleFormationUtilisateur)) {
            $this->moduleFormationUtilisateurs->add($moduleFormationUtilisateur);
            $moduleFormationUtilisateur->setModuleFormation($this);
        }

        return $this;
    }

    public function removeModuleFormationUtilisateur(ModuleFormationUtilisateur $moduleFormationUtilisateur): self
    {
        if ($this->moduleFormationUtilisateurs->removeElement($moduleFormationUtilisateur)) {
            if ($moduleFormationUtilisateur->getModuleFormation() === $this) {
                $moduleFormationUtilisateur->setModuleFormation(null);
            }
        }

        return $this;
    }
}
