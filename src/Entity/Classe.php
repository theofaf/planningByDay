<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["nelmio", "classe", "cursus", "session"])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups(["nelmio", "classe", "session"])]
    private ?string $libelle = null;

    #[ORM\Column]
    #[Groups(["nelmio", "classe"])]
    private ?int $nombreEleves = null;

    #[ORM\ManyToOne(targetEntity: Cursus::class, inversedBy: 'classes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "classe"])]
    private ?Cursus $cursus = null;

    #[ORM\OneToMany(mappedBy: 'classe', targetEntity: Session::class, cascade: ['remove'])]
    #[Groups(["nelmio", "classe"])]
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

    public function getNombreEleves(): ?int
    {
        return $this->nombreEleves;
    }

    public function setNombreEleves(int $nombreEleves): self
    {
        $this->nombreEleves = $nombreEleves;

        return $this;
    }

    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    public function setCursus(?Cursus $cursus): self
    {
        $this->cursus = $cursus;

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
            $session->setClasse($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session)) {
            if ($session->getClasse() === $this) {
                $session->setClasse(null);
            }
        }

        return $this;
    }
}
