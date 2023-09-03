<?php

namespace App\DataFixtures;

use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\ModuleFormation;
use Exception;
use Faker\Factory;
use Faker\Generator;

class ModuleFormationFixtures extends Fixture implements DependentFixtureInterface
{
    private ?Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public const REFERENCE_MODULE_FORMATION_PROGRAMMATION = 'Programmation';
    public const REFERENCE_MODULE_FORMATION_MARKETING_RELATIONNEL = 'Marketing relationnel';
    public const REFERENCE_MODULE_FORMATION_ANGLAIS_AVANCE = 'Anglais avancé';
    public const REFERENCE_MODULE_FORMATION_TRADING = 'Trading';
    public const REFERENCE_MODULE_FORMATION_COMMUNICATION = 'Communication';

    public const LISTE_FORMATIONS_MODULES = [
        self::REFERENCE_MODULE_FORMATION_PROGRAMMATION,
        self::REFERENCE_MODULE_FORMATION_MARKETING_RELATIONNEL,
        self::REFERENCE_MODULE_FORMATION_ANGLAIS_AVANCE,
        self::REFERENCE_MODULE_FORMATION_TRADING,
        self::REFERENCE_MODULE_FORMATION_COMMUNICATION,
    ];

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $moduleData = [
            [
                'libelle' => self::REFERENCE_MODULE_FORMATION_PROGRAMMATION,
                'duree' => $this->genererAleaDureeModuleFormation(),
                'listeCursus' => [
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_INFORMATIQUE),
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_GESTION),
                ],
            ],
            [
                'libelle' => self::REFERENCE_MODULE_FORMATION_MARKETING_RELATIONNEL,
                'duree' => $this->genererAleaDureeModuleFormation(),
                'listeCursus' => [
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_GESTION),
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_MARKETING),
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_MANAGEMENT),
                ],
            ],
            [
                'libelle' => self::REFERENCE_MODULE_FORMATION_ANGLAIS_AVANCE,
                'duree' => $this->genererAleaDureeModuleFormation(),
                'listeCursus' => [
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_LANGUES),
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_MARKETING),
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_MANAGEMENT),
                ],
            ],
            [
                'libelle' => self::REFERENCE_MODULE_FORMATION_TRADING,
                'duree' => $this->genererAleaDureeModuleFormation(),
                'listeCursus' => [
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_FINANCE),
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_LANGUES),
                ],
            ],
            [
                'libelle' => self::REFERENCE_MODULE_FORMATION_COMMUNICATION,
                'duree' => $this->genererAleaDureeModuleFormation(),
                'listeCursus' => [
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_LANGUES),
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_INFORMATIQUE),
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_MARKETING),
                    $this->getReference(CursusFixtures::REFERENCE_CURSUS_MANAGEMENT),
                ],
            ],
        ];

        foreach ($moduleData as $data) {
            $module = (new ModuleFormation())
                ->setLibelle($data['libelle'])
                ->setListeCursus($data['listeCursus'])
                ->setDuree($data['duree'])
            ;
            $manager->persist($module);
            $this->addReference($module->getLibelle(), $module);
        }

        $manager->flush();
    }

    /**
     * Génère un DateTime avec une durée aléatoire entre 1h et 10h, avec les minutes fixées à zéro.
     * @return DateTime La date et l'heure de fin du module.
     * @throws Exception
     */
    private function genererAleaDureeModuleFormation(): DateTime
    {
        $nbHeureAlea = $this->faker->numberBetween(1, 10);
        return DateTime::createFromFormat('H', $nbHeureAlea);
    }

    public function getDependencies(): array
    {
        return [
            CursusFixtures::class,
        ];
    }
}
