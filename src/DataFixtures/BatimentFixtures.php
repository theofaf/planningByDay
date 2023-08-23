<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Batiment;

class BatimentFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_BATIMENT_HUGO_A = 'A';
    public const REFERENCE_BATIMENT_HUGO_B = 'B';
    public const REFERENCE_BATIMENT_HUGO_C = 'C';
    public const REFERENCE_BATIMENT_CAMUS_1 = '1';
    public const REFERENCE_BATIMENT_CAMUS_2 = '2';
    public const REFERENCE_BATIMENT_ZOLA_A = 'Nana';
    public const REFERENCE_BATIMENT_ZOLA_B = 'La terre';
    public const REFERENCE_BATIMENT_SARTHE_1 = 'Champion';
    public const REFERENCE_BATIMENT_PROUST_A = 'La Madeleine';

    public function load(ObjectManager $manager): void
    {
        $batimentsData = [
            [
                'etablissement' => $this->getReference(EtablissementFixtures::REFERENCE_ECOLE_HUGO),
                'libelle' => self::REFERENCE_BATIMENT_HUGO_A,
                'num_voie' => 789,
                'rue' => 'Rue des Montagnes',
                'ville' => 'Toulouse',
                'code_postal' => 31000,
                'numeroTel' => '0598653298',
            ],
            [
                'etablissement' => $this->getReference(EtablissementFixtures::REFERENCE_ECOLE_HUGO),
                'libelle' => self::REFERENCE_BATIMENT_HUGO_B,
                'num_voie' => 790,
                'rue' => 'Rue des Montagnes',
                'ville' => 'Toulouse',
                'code_postal' => 31000,
                'numeroTel' => '0598653299',
            ],
            [
                'etablissement' => $this->getReference(EtablissementFixtures::REFERENCE_ECOLE_HUGO),
                'libelle' => self::REFERENCE_BATIMENT_HUGO_C,
                'num_voie' => 791,
                'rue' => 'Rue des Montagnes',
                'ville' => 'Toulouse',
                'code_postal' => 31000,
                'numeroTel' => '0598653300',
            ],
            [
                'etablissement' => $this->getReference(EtablissementFixtures::REFERENCE_ECOLE_CAMUS),
                'libelle' => self::REFERENCE_BATIMENT_CAMUS_1,
                'num_voie' => 123,
                'rue' => 'Rue des lilas',
                'ville' => 'Paris',
                'code_postal' => 75000,
                'numeroTel' => '0123456789',
            ],
            [
                'etablissement' => $this->getReference(EtablissementFixtures::REFERENCE_ECOLE_CAMUS),
                'libelle' => self::REFERENCE_BATIMENT_CAMUS_2,
                'num_voie' => 124,
                'rue' => 'Rue des lilas',
                'ville' => 'Paris',
                'code_postal' => 75000,
                'numeroTel' => '0123456790',
            ],
            [
                'etablissement' => $this->getReference(EtablissementFixtures::REFERENCE_UNIVERSITE_ZOLA),
                'libelle' => self::REFERENCE_BATIMENT_ZOLA_A,
                'num_voie' => 456,
                'rue' => 'Rue des peupliers',
                'ville' => 'Nantes',
                'code_postal' => 44200,
                'numeroTel' => '0598653298',
            ],
            [
                'etablissement' => $this->getReference(EtablissementFixtures::REFERENCE_UNIVERSITE_ZOLA),
                'libelle' => self::REFERENCE_BATIMENT_ZOLA_B,
                'num_voie' => 456,
                'rue' => 'Rue des peupliers',
                'ville' => 'Nantes',
                'code_postal' => 44200,
                'numeroTel' => '0598653299',
            ],
            [
                'etablissement' => $this->getReference(EtablissementFixtures::REFERENCE_LYCEE_SARTHE),
                'libelle' => self::REFERENCE_BATIMENT_SARTHE_1,
                'num_voie' => 101,
                'rue' => 'Rue des Océans',
                'ville' => 'Rennes',
                'code_postal' => 35000,
                'numeroTel' => '0226986554',
            ],
            [
                'etablissement' => $this->getReference(EtablissementFixtures::REFERENCE_UNIVERSITE_PROUST),
                'libelle' => self::REFERENCE_BATIMENT_PROUST_A,
                'num_voie' => 202,
                'rue' => 'Rue des Étoiles',
                'ville' => 'Lille',
                'code_postal' => 59000,
                'numeroTel' => '0312548787',
            ],
        ];

        foreach ($batimentsData as $batimentData) {
            $batiment = (new Batiment())
                ->setEtablissement($batimentData['etablissement'])
                ->setLibelle($batimentData['libelle'])
                ->setNumVoie($batimentData['num_voie'])
                ->setRue($batimentData['rue'])
                ->setVille($batimentData['ville'])
                ->setCodePostal($batimentData['code_postal'])
                ->setNumeroTel($batimentData['numeroTel'])
            ;

            $manager->persist($batiment);
            $this->addReference($batiment->getLibelle(), $batiment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EtablissementFixtures::class,
        ];
    }
}
