<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Salle;
use App\Entity\Batiment;
use Faker\Factory;
use Faker\Generator;

class SalleFixtures extends Fixture implements DependentFixtureInterface
{
    private ?Generator $faker;

    public function __construct(private readonly EntityManagerInterface $em)
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $batiments = $this->em->getRepository(Batiment::class)->findAll();

        foreach ($batiments as $batiment) {
            for ($i = 1; $i <= $this->faker->numberBetween(1, 20); $i++) {
                $salle = (new Salle())
                    ->setBatiment($batiment)
                    ->setLibelle($this->faker->unique()->lastName())
                    ->setNbPlace($this->faker->numberBetween(75, 100))
                    ->setEquipementInfo($this->genererAleaEquipementInfo())
                ;
                $manager->persist($salle);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BatimentFixtures::class,
        ];
    }

    private function genererAleaEquipementInfo(): string
    {
        return
            'tableau : ' . ($this->faker->numberBetween(0, 2)) .
            ', vidéoprojecteur : ' . ($this->faker->numberBetween(0, 2)) .
            ', micro : ' . ($this->faker->numberBetween(0, 2))
        ;
    }
}
