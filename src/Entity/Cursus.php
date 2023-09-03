<?php

namespace App\Entity;

use App\Repository\CursusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CursusRepository::class)]
class Cursus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'cursus', targetEntity: Classe::class)]
    /** @var ArrayCollection $classes */
    private $classes;

    #[ORM\ManyToMany(targetEntity: ModuleFormation::class, mappedBy: 'listeCursus')]
    /** @var ArrayCollection $moduleFormations */
    private $moduleFormations;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->moduleFormations = new ArrayCollection();
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

    /**
     * @return ArrayCollection<Classe>
     */
    public function getClasses()
    {
        return $this->classes;
    }

    public function addClasse(Classe $classe): self
    {
        if (!$this->classes->contains($classe)) {
            $this->classes->add($classe);
            $classe->setCursus($this);
        }

        return $this;
    }

    public function removeClasse(Classe $classe): self
    {
        if ($this->classes->removeElement($classe)) {
            if ($classe->getCursus() === $this) {
                $classe->setCursus(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection<ModuleFormation>
     */
    public function getModuleFormations()
    {
        return $this->moduleFormations;
    }

    public function addModuleFormation(ModuleFormation $moduleFormation): self
    {
        if (!$this->moduleFormations->contains($moduleFormation)) {
            $this->moduleFormations->add($moduleFormation);
            $moduleFormation->addCursus($this);
        }

        return $this;
    }

    public function removeModuleFormation(ModuleFormation $moduleFormation): self
    {
        if ($this->moduleFormations->removeElement($moduleFormation)) {
            $moduleFormation->removeCursus($this);
        }

        return $this;
    }
}
