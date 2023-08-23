<?php

namespace App\Entity;

use App\Repository\EtablissementRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtablissementRepository::class)]
class Etablissement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?int $numVoie = null;

    #[ORM\Column(length: 50)]
    private ?string $rue = null;

    #[ORM\Column(length: 30)]
    private ?string $ville = null;

    #[ORM\Column]
    private ?int $codePostal = null;

    #[ORM\Column]
    private ?string $numeroTel = null;

    #[ORM\Column]
    private ?bool $statutAbonnement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dateAbonnement = null;

    #[ORM\ManyToOne]
    private ?Abonnement $abonnement = null;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Ticket::class)]
    /** @var ArrayCollection $tickets */
    private $tickets;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Batiment::class)]
    /** @var ArrayCollection $batiments */
    private $batiments;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Utilisateur::class)]
    /** @var ArrayCollection $utilisateurs */
    private $utilisateurs;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->batiments = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
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

    public function setNumeroTel(string $numeroTel): self
    {
        $this->numeroTel = $numeroTel;

        return $this;
    }

    public function isStatutAbonnement(): ?bool
    {
        return $this->statutAbonnement;
    }

    public function setStatutAbonnement(bool $statutAbonnement): self
    {
        $this->statutAbonnement = $statutAbonnement;

        return $this;
    }

    public function getDateAbonnement(): ?DateTimeInterface
    {
        return $this->dateAbonnement;
    }

    public function setDateAbonnement(?DateTimeInterface $dateAbonnement): self
    {
        $this->dateAbonnement = $dateAbonnement;

        return $this;
    }

    public function getAbonnement(): ?Abonnement
    {
        return $this->abonnement;
    }

    public function setAbonnement(?Abonnement $abonnement): self
    {
        $this->abonnement = $abonnement;

        return $this;
    }

    public function getTickets(): ?ArrayCollection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setEtablissement($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            if ($ticket->getEtablissement() === $this) {
                $ticket->setEtablissement(null);
            }
        }

        return $this;
    }

    public function getBatiments(): ?ArrayCollection
    {
        return $this->batiments;
    }

    public function addBatiment(Batiment $batiment): self
    {
        if (!$this->batiments->contains($batiment)) {
            $this->batiments->add($batiment);
            $batiment->setEtablissement($this);
        }

        return $this;
    }

    public function removeBatiment(Batiment $batiment): self
    {
        if ($this->batiments->removeElement($batiment)) {
            if ($batiment->getEtablissement() === $this) {
                $batiment->setEtablissement(null);
            }
        }

        return $this;
    }

    public function getUtilisateurs(): ?ArrayCollection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setEtablissement($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            if ($utilisateur->getEtablissement() === $this) {
                $utilisateur->setEtablissement(null);
            }
        }

        return $this;
    }
}
