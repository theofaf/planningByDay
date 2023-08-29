<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Statut;

class StatutFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statutsData = [
            Statut::STATUT_BROUILLON,
            Statut::STATUT_PUBLIE,
            Statut::STATUT_VALIDE,
            Statut::STATUT_FERME,
            Statut::STATUT_ANNULE,
        ];

        foreach ($statutsData as $statutData) {
            $statut = (new Statut())->setLibelle($statutData);
            $manager->persist($statut);
            $this->addReference($statutData, $statut);
        }

        $manager->flush();
    }
}
