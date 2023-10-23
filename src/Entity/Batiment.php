<?php

namespace App\Entity;

use App\Repository\BatimentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BatimentRepository::class)]
class Batiment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["nelmio", "batiment", "etablissement", "salle"])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups(["nelmio", "batiment"])]
    private ?string $libelle = null;

    #[ORM\Column]
    #[Groups(["nelmio", "batiment"])]
    private ?int $numVoie = null;

    #[ORM\Column(length: 30)]
    #[Groups(["nelmio", "batiment"])]
    private ?string $rue = null;

    #[ORM\Column(length: 30)]
    #[Groups(["nelmio", "batiment"])]
    private ?string $ville = null;

    #[ORM\Column]
    #[Groups(["nelmio", "batiment"])]
    private ?int $codePostal = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["nelmio", "batiment"])]
    private ?string $numeroTel = null;

    #[ORM\ManyToOne(targetEntity: Etablissement::class, inversedBy: 'batiments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "batiment"])]
    private ?Etablissement $etablissement = null;

    #[ORM\OneToMany(mappedBy: 'batiment', targetEntity: Salle::class, cascade: ['remove'])]
    #[Groups(["nelmio", "batiment"])]
    /** @var ArrayCollection $salles */
    private $salles;

    public function __construct()
    {
        $this->salles = new ArrayCollection();
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

    public function getNumVoie(): ?int
    {
        return $this->numVoie;
    }

    public function setNumVoie(int $numVoie): self
    {
        $this->numVoie = $numVoie;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getNumeroTel(): ?string
    {
        return $this->numeroTel;
    }

    public function setNumeroTel(?string $numeroTel): self
    {
        $this->numeroTel = $numeroTel;

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


    /** @return ArrayCollection<Salle> */
    public function getSalles()
    {
        return $this->salles;
    }

    public function addSalle(Salle $salle): self
    {
        if (!$this->salles->contains($salle)) {
            $this->salles->add($salle);
            $salle->setBatiment($this);
        }

        return $this;
    }

    public function removeSalle(Salle $salle): self
    {
        if ($this->salles->removeElement($salle)) {
            if ($salle->getBatiment() === $this) {
                $salle->setBatiment(null);
            }
        }

        return $this;
    }
}
