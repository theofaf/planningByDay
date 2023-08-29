<?php

namespace App\Entity;

use App\Repository\StatutRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatutRepository::class)]
class Statut
{
    public const STATUT_BROUILLON = 'brouillon';
    public const STATUT_PUBLIE = 'publie';
    public const STATUT_FERME= 'ferme';
    public const STATUT_ANNULE = 'annule';
    public const STATUT_VALIDE = 'valide';

    public const LISTE_STATUT_MESSAGE = [
      self::STATUT_BROUILLON,
      self::STATUT_PUBLIE,
      self::STATUT_FERME,
      self::STATUT_ANNULE,
      self::STATUT_VALIDE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $libelle = null;

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
}
