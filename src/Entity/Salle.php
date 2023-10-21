<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["nelmio", "salle", "batiment"])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups(["nelmio", "salle"])]
    private ?string $libelle = null;

    #[ORM\Column]
    #[Groups(["nelmio", "salle"])]
    private ?int $nbPlace = null;

    #[ORM\Column]
    #[Groups(["nelmio", "salle"])]
    private ?string $equipementInfo = null;

    #[ORM\ManyToOne(targetEntity: Batiment::class, inversedBy: 'salles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "salle"])]
    private ?Batiment $batiment = null;

    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: Session::class)]
    #[Groups(["nelmio", "salle"])]
    /** @var ArrayCollection $sessions */
    private $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
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

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): self
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getEquipementInfo(): ?string
    {
        return $this->equipementInfo;
    }

    public function setEquipementInfo(string $equipementInfo): self
    {
        $this->equipementInfo = $equipementInfo;

        return $this;
    }

    public function getBatiment(): ?Batiment
    {
        return $this->batiment;
    }

    public function setBatiment(?Batiment $batiment): self
    {
        $this->batiment = $batiment;

        return $this;
    }

    /** @return ArrayCollection<Session> */
    public function getSessions()
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setSalle($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session)) {
            if ($session->getSalle() === $this) {
                $session->setSalle(null);
            }
        }

        return $this;
    }
}
