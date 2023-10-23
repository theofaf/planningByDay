<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement
{
    public const LICENCE_ESSENTIELLE_ID = 1;
    public const LICENCE_AVANCEE_ID = 2;
    public const LICENCE_PREMIUM_ID = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["nelmio", "abonnement", "etablissement"])]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Groups(["nelmio", "abonnement"])]
    private ?string $libelle = null;

    #[ORM\Column(length: 20)]
    #[Groups(["nelmio", "abonnement"])]
    private ?string $libelleTechnique = null;

    #[ORM\Column]
    #[Groups(["nelmio", "abonnement"])]
    private ?float $prix = null;

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

    public function getLibelleTechnique(): ?string
    {
        return $this->libelleTechnique;
    }

    public function setLibelleTechnique(?string $libelleTechnique): self
    {
        $this->libelleTechnique = $libelleTechnique;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }
}
