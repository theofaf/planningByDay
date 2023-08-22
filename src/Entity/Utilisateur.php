<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'idReceveur', targetEntity: Message::class)]
    private Collection $idReceveur;

    #[ORM\OneToMany(mappedBy: 'idEmetteur', targetEntity: Message::class)]
    private Collection $idEmetteur;

    #[ORM\OneToMany(mappedBy: 'idUtilisateur', targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\ManyToMany(targetEntity: ModuleFormation::class, mappedBy: 'idModuleFormation_Utilisateur')]
    private Collection $moduleFormations;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etablissement $idEtablissement = null;

    #[ORM\OneToMany(mappedBy: 'idUtilisateur', targetEntity: Session::class)]
    private Collection $sessions;

    public function __construct()
    {
        $this->idReceveur = new ArrayCollection();
        $this->idEmetteur = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->moduleFormations = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getIdReceveur(): Collection
    {
        return $this->idReceveur;
    }

    public function addIdReceveur(Message $idReceveur): static
    {
        if (!$this->idReceveur->contains($idReceveur)) {
            $this->idReceveur->add($idReceveur);
            $idReceveur->setIdReceveur($this);
        }

        return $this;
    }

    public function removeIdReceveur(Message $idReceveur): static
    {
        if ($this->idReceveur->removeElement($idReceveur)) {
            // set the owning side to null (unless already changed)
            if ($idReceveur->getIdReceveur() === $this) {
                $idReceveur->setIdReceveur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getIdEmetteur(): Collection
    {
        return $this->idEmetteur;
    }

    public function addIdEmetteur(Message $idEmetteur): static
    {
        if (!$this->idEmetteur->contains($idEmetteur)) {
            $this->idEmetteur->add($idEmetteur);
            $idEmetteur->setIdEmetteur($this);
        }

        return $this;
    }

    public function removeIdEmetteur(Message $idEmetteur): static
    {
        if ($this->idEmetteur->removeElement($idEmetteur)) {
            // set the owning side to null (unless already changed)
            if ($idEmetteur->getIdEmetteur() === $this) {
                $idEmetteur->setIdEmetteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setIdUtilisateur($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getIdUtilisateur() === $this) {
                $ticket->setIdUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ModuleFormation>
     */
    public function getModuleFormations(): Collection
    {
        return $this->moduleFormations;
    }

    public function addModuleFormation(ModuleFormation $moduleFormation): static
    {
        if (!$this->moduleFormations->contains($moduleFormation)) {
            $this->moduleFormations->add($moduleFormation);
            $moduleFormation->addIdModuleFormationUtilisateur($this);
        }

        return $this;
    }

    public function removeModuleFormation(ModuleFormation $moduleFormation): static
    {
        if ($this->moduleFormations->removeElement($moduleFormation)) {
            $moduleFormation->removeIdModuleFormationUtilisateur($this);
        }

        return $this;
    }

    public function getIdEtablissement(): ?Etablissement
    {
        return $this->idEtablissement;
    }

    public function setIdEtablissement(?Etablissement $idEtablissement): static
    {
        $this->idEtablissement = $idEtablissement;

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setIdUtilisateur($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getIdUtilisateur() === $this) {
                $session->setIdUtilisateur(null);
            }
        }

        return $this;
    }
}
