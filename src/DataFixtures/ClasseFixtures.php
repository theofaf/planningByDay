<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Classe;
use Faker\Factory;
use Faker\Generator;

class ClasseFixtures extends Fixture implements DependentFixtureInterface
{
    private ?Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $cursusReferences = CursusFixtures::LISTE_REFERENCES_CURSUS;

        foreach ($cursusReferences as $cursusReference) {
            $nbClasseAlea = $this->faker->numberBetween(2, 5);
            for ($i = 0; $i < $nbClasseAlea; $i++) {
                $classe = (new Classe())
                    ->setLibelle($this->faker->unique()->colorName())
                    ->setNombreEleves($this->faker->numberBetween(10, 75))
                    ->setCursus($this->getReference($cursusReference))
                ;
                $manager->persist($classe);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CursusFixtures::class,
        ];
    }
}
