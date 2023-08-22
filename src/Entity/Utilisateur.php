<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
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

    #[ORM\OneToMany(mappedBy: 'receveur', targetEntity: Message::class)]
    private $messagesRecues;

    #[ORM\OneToMany(mappedBy: 'emetteur', targetEntity: Message::class)]
    private $messagesEnvoyees;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Ticket::class)]
    /** @var ArrayCollection $tickets */
    private $tickets;

    #[ORM\ManyToMany(targetEntity: ModuleFormation::class, mappedBy: 'listeUtilisateurs')]
    /** @var ArrayCollection $moduleFormations */
    private $moduleFormations;

    #[ORM\ManyToOne(targetEntity: Etablissement::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etablissement $etablissement = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Session::class)]
    /** @var ArrayCollection $sessions */
    private $sessions;

    public function __construct()
    {
        $this->messagesRecues = new ArrayCollection();
        $this->messagesEnvoyees = new ArrayCollection();
        $this->moduleFormations = new ArrayCollection();
        $this->tickets = new ArrayCollection();
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

    public function setEmail(string $email): self
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

    public function setRoles(array $roles): self
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

    public function setPassword(string $password): self
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

    public function getMessagesRecues(): ?ArrayCollection
    {
        return $this->messagesRecues;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messagesEnvoyees->contains($message)) {
            $this->messagesEnvoyees->add($message);
            $message->setReceveur($message->getReceveur());
            $message->setEmetteur($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messagesEnvoyees->removeElement($message)) {
            if ($message->getReceveur() === $this) {
                $message->setReceveur(null);
                $message->setEmetteur(null);
            }
        }

        return $this;
    }

    public function getMessagesEnvoyees(): ?ArrayCollection
    {
        return $this->messagesEnvoyees;
    }

    public function getTickets(): ?ArrayCollection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setUtilisateur($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            if ($ticket->getUtilisateur() === $this) {
                $ticket->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getModuleFormations(): ?ArrayCollection
    {
        return $this->moduleFormations;
    }

    public function addModuleFormation(ModuleFormation $moduleFormation): self
    {
        if (!$this->moduleFormations->contains($moduleFormation)) {
            $this->moduleFormations->add($moduleFormation);
            $moduleFormation->addUtilisateur($this);
        }

        return $this;
    }

    public function removeModuleFormation(ModuleFormation $moduleFormation): self
    {
        if ($this->moduleFormations->removeElement($moduleFormation)) {
            $moduleFormation->removeUtilisateur($this);
        }

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

    public function getSessions(): ?ArrayCollection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setUtilisateur($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session)) {
            if ($session->getUtilisateur() === $this) {
                $session->setUtilisateur(null);
            }
        }

        return $this;
    }
}
