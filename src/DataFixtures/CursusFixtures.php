<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Cursus;

class CursusFixtures extends Fixture
{
    public const REFERENCE_CURSUS_INFORMATIQUE = 'Informatique';
    public const REFERENCE_CURSUS_GESTION = 'Gestion';
    public const REFERENCE_CURSUS_LANGUES = 'Langues';
    public const REFERENCE_CURSUS_MARKETING = 'Marketing';
    public const REFERENCE_CURSUS_MANAGEMENT = 'Management';
    public const REFERENCE_CURSUS_FINANCE = 'Finance';

    public const LISTE_REFERENCES_CURSUS = [
        self::REFERENCE_CURSUS_INFORMATIQUE,
        self::REFERENCE_CURSUS_GESTION,
        self::REFERENCE_CURSUS_LANGUES,
        self::REFERENCE_CURSUS_MARKETING,
        self::REFERENCE_CURSUS_MANAGEMENT,
        self::REFERENCE_CURSUS_FINANCE,
    ];

    public function load(ObjectManager $manager): void
    {
        $cursusData = self::LISTE_REFERENCES_CURSUS;
        foreach ($cursusData as $data) {
            $cursus = (new Cursus())->setLibelle($data);
            $manager->persist($cursus);
            $this->addReference($cursus->getLibelle(), $cursus);
        }

        $manager->flush();
    }
}
