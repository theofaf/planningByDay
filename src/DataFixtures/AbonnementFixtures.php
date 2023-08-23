<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Abonnement;

class AbonnementFixtures extends Fixture
{
    public const REFERENCE_ABONNEMENT_ESSENTIELLE_LIBELLE = 'Essentielle';
    public const REFERENCE_ABONNEMENT_AVANCEE_LIBELLE = 'Avancée';
    public const REFERENCE_ABONNEMENT_PREMIUM_LIBELLE = 'Premium';

    public function load(ObjectManager $manager)
    {
        $abonnementsData = [
            [
                'libelle' => self::REFERENCE_ABONNEMENT_ESSENTIELLE_LIBELLE,
                'prix' => 5000,
            ],
            [
                'libelle' => self::REFERENCE_ABONNEMENT_AVANCEE_LIBELLE,
                'prix' => 7500,
            ],
            [
                'libelle' => self::REFERENCE_ABONNEMENT_PREMIUM_LIBELLE,
                'prix' => 10000,
            ],
        ];

        foreach ($abonnementsData as $abonnementData) {
            $abonnement = (new Abonnement())
                ->setLibelle($abonnementData['libelle'])
                ->setPrix($abonnementData['prix']);
            $manager->persist($abonnement);
            $this->addReference($abonnement->getLibelle(), $abonnement);
        }

        $manager->flush();
    }
}
