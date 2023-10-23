<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLES_POSSIBLE = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPPORT', 'ROLE_RESPONSABLE_PLANNING'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["nelmio", "utilisateur", "etablissement", "ticket", "message", "ModuleFormationUtilisateur", "session"])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["nelmio", "utilisateur", "ModuleFormationUtilisateur"])]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    #[Groups(["nelmio", "utilisateur", "ModuleFormationUtilisateur", "session"])]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Groups(["nelmio", "utilisateur", "ModuleFormationUtilisateur", "session"])]
    private ?string $prenom = null;

    #[ORM\Column]
    #[Groups(["nelmio", "utilisateur"])]
    /** @var string[] $roles */
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'receveur', targetEntity: Message::class, cascade: ['remove'])]
    #[Groups(["nelmio", "utilisateur"])]
    private $messagesRecues;

    #[ORM\OneToMany(mappedBy: 'emetteur', targetEntity: Message::class, cascade: ['remove'])]
    #[Groups(["nelmio", "utilisateur"])]
    private $messagesEnvoyees;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Ticket::class, cascade: ['remove'])]
    #[Groups(["nelmio", "utilisateur"])]
    /** @var ArrayCollection $tickets */
    private $tickets;

    #[ORM\ManyToOne(targetEntity: Etablissement::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["nelmio", "utilisateur"])]
    private ?Etablissement $etablissement = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Session::class, cascade: ['remove'])]
    #[Groups(["nelmio", "utilisateur"])]
    /** @var ArrayCollection $sessions */
    private $sessions;

    /**
     * Permet de savoir si on doit fait expirer le token d'authentification
     * (si la dernière action date de plus de 10 min)
     */
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["nelmio", "utilisateur"])]
    private ?DateTimeInterface $dateDerniereAction = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: ModuleFormationUtilisateur::class, cascade: ['remove'])]
    #[Groups(["nelmio", "utilisateur"])]
    /** @var ArrayCollection $listeModulesFormations */
    private $listeModulesFormations;

    public function __construct()
    {
        $this->messagesRecues = new ArrayCollection();
        $this->messagesEnvoyees = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->listeModulesFormations = new ArrayCollection();
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

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

    /** @return ArrayCollection<Message> */
    public function getMessagesRecues()
    {
        return $this->messagesRecues;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messagesEnvoyees->contains($message)) {
            $this->messagesEnvoyees->add($message);
            $message->getReceveur()->addMessage($message);
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

    /** @return ArrayCollection<Message> */
    public function getMessagesEnvoyees()
    {
        return $this->messagesEnvoyees;
    }

    /** @return ArrayCollection<Ticket> */
    public function getTickets()
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

    public function getEtablissement(): ?Etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?Etablissement $etablissement): self
    {
        $this->etablissement = $etablissement;

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

    public function getDateDerniereAction(): ?DateTimeInterface
    {
        return $this->dateDerniereAction;
    }

    public function setDateDerniereAction(?DateTimeInterface $dateDerniereAction): self
    {
        $this->dateDerniereAction = $dateDerniereAction;

        return $this;
    }

    /** @return ArrayCollection<ModuleFormationUtilisateur> */
    public function getListeModulesFormations()
    {
        return $this->listeModulesFormations;
    }

    public function addListeModulesFormation(ModuleFormationUtilisateur $moduleFormationUtilisateur): self
    {
        if (!$this->listeModulesFormations->contains($moduleFormationUtilisateur)) {
            $this->listeModulesFormations->add($moduleFormationUtilisateur);
            $moduleFormationUtilisateur->setUtilisateur($this);
        }

        return $this;
    }

    public function removeListeModulesFormation(ModuleFormationUtilisateur $listeModulesFormation): self
    {
        if ($this->listeModulesFormations->removeElement($listeModulesFormation)) {
            if ($listeModulesFormation->getUtilisateur() === $this) {
                $listeModulesFormation->setUtilisateur(null);
            }
        }

        return $this;
    }
}
