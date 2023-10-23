<?php

namespace App\DataFixtures;

use App\Repository\AbonnementRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Etablissement;

class EtablissementFixtures extends Fixture
{
    public const REFERENCE_ECOLE_CAMUS = 'École Albert Camus';
    public const REFERENCE_UNIVERSITE_ZOLA = 'Université Émile Zola';
    public const REFERENCE_ECOLE_HUGO = 'École Victor Hugo';
    public const REFERENCE_LYCEE_SARTHE = 'Lycée Jean-Paul Sartre';
    public const REFERENCE_UNIVERSITE_PROUST = 'Université Marcel Proust';

    public const LISTE_REFERENCES_ETABLISSEMENT = [
        self::REFERENCE_ECOLE_CAMUS,
        self::REFERENCE_UNIVERSITE_ZOLA,
        self::REFERENCE_ECOLE_HUGO,
        self::REFERENCE_LYCEE_SARTHE,
        self::REFERENCE_UNIVERSITE_PROUST,
    ];

    public function __construct(
        private readonly AbonnementRepository $abonnementRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $abonnements = $this->abonnementRepository->findAll();
        $etablissementsData = [
            [
                'libelle' => Etablissement::REFERENCE_SANS_AFFECTION,
                'numVoie' => 0,
                'rue' => 'inconnu',
                'ville' => 'inconnu',
                'codePostal' => 00000,
                'numeroTel' => '0000000000',
                'statutAbonnement' => false,
                'abonnement' => $abonnements[0],
                'dateAbonnement' => null,
            ],
            [
                'libelle' => self::REFERENCE_ECOLE_CAMUS,
                'numVoie' => 123,
                'rue' => 'Rue des lilas',
                'ville' => 'Paris',
                'codePostal' => 75000,
                'numeroTel' => '0123456789',
                'statutAbonnement' => true,
                'abonnement' => $abonnements[0],
                'dateAbonnement' => new DateTime(),
            ],
            [
                'libelle' => self::REFERENCE_UNIVERSITE_ZOLA,
                'numVoie' => 456,
                'rue' => 'Rue des peupliers',
                'ville' => 'Nantes',
                'codePostal' => 44200,
                'numeroTel' => '0248986532',
                'statutAbonnement' => true,
                'abonnement' => $abonnements[0],
                'dateAbonnement' => new DateTime('-2 years'),
            ],
            [
                'libelle' => self::REFERENCE_ECOLE_HUGO,
                'numVoie' => 789,
                'rue' => 'Rue des Montagnes',
                'ville' => 'Toulouse',
                'codePostal' => 31000,
                'numeroTel' => '0598653298',
                'statutAbonnement' => true,
                'abonnement' => $abonnements[1],
                'dateAbonnement' => new DateTime('-1 year -6 months'),
            ],
            [
                'libelle' => self::REFERENCE_LYCEE_SARTHE,
                'numVoie' => 101,
                'rue' => 'Rue des Océans',
                'ville' => 'Rennes',
                'codePostal' => 35000,
                'numeroTel' => '0226986554',
                'statutAbonnement' => true,
                'abonnement' => $abonnements[1],
                'dateAbonnement' => new DateTime('-1 year -3 months'),

            ],
            [
                'libelle' => self::REFERENCE_UNIVERSITE_PROUST,
                'numVoie' => 202,
                'rue' => 'Rue des Étoiles',
                'ville' => 'Lille',
                'codePostal' => 59000,
                'numeroTel' => '0312548787',
                'statutAbonnement' => true,
                'abonnement' => $abonnements[2],
                'dateAbonnement' => new DateTime('-1 year -15 days'),
            ],
        ];

        foreach ($etablissementsData as $etablissementData) {
            $etablissement = (new Etablissement())
                ->setLibelle($etablissementData['libelle'])
                ->setNumVoie($etablissementData['numVoie'])
                ->setRue($etablissementData['rue'])
                ->setVille($etablissementData['ville'])
                ->setCodePostal($etablissementData['codePostal'])
                ->setNumeroTel($etablissementData['numeroTel'])
                ->setStatutAbonnement($etablissementData['statutAbonnement'])
                ->setDateAbonnement($etablissementData['dateAbonnement'])
                ->setAbonnement($etablissementData['abonnement'])
            ;

            $manager->persist($etablissement);
            $this->addReference($etablissement->getLibelle(), $etablissement);
        }

        $manager->flush();
    }
}
