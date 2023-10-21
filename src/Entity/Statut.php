<?php

namespace App\Entity;

use App\Repository\StatutRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StatutRepository::class)]
class Statut
{
    public const STATUT_BROUILLON_TECHNIQUE = 'brouillon';
    public const STATUT_PUBLIE_TECHNIQUE = 'publie';
    public const STATUT_FERME_TECHNIQUE = 'ferme';
    public const STATUT_ANNULE_TECHNIQUE = 'annule';
    public const STATUT_VALIDE_TECHNIQUE = 'valide';

    public const STATUT_BROUILLON_FRANCISE = 'brouillon';
    public const STATUT_PUBLIE_FRANCISE = 'publié';
    public const STATUT_FERME_FRANCISE = 'fermé';
    public const STATUT_ANNULE_FRANCISE = 'annulé';
    public const STATUT_VALIDE_FRANCISE = 'validé';

    public const STATUT_PUBLIE_ID = 2;

    public const LISTE_STATUT_MESSAGE = [
      self::STATUT_BROUILLON_TECHNIQUE => self::STATUT_BROUILLON_FRANCISE,
      self::STATUT_PUBLIE_TECHNIQUE => self::STATUT_PUBLIE_FRANCISE,
      self::STATUT_FERME_TECHNIQUE => self::STATUT_FERME_FRANCISE,
      self::STATUT_ANNULE_TECHNIQUE => self::STATUT_ANNULE_FRANCISE,
      self::STATUT_VALIDE_TECHNIQUE => self::STATUT_VALIDE_FRANCISE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["nelmio", "statut", "ticket", "message"])]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    #[Groups(["nelmio", "statut"])]
    private ?string $libelle = null;

    #[ORM\Column(length: 15)]
    #[Groups(["nelmio", "statut"])]
    private ?string $libelleTechnique = null;

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
}
