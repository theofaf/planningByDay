<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Faker\Generator;

class UtilisateurFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;

        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $moduleReferences = [
            ModuleFormationFixtures::REFERENCE_MODULE_FORMATION_PROGRAMMATION,
            ModuleFormationFixtures::REFERENCE_MODULE_FORMATION_MARKETING_RELATIONNEL,
            ModuleFormationFixtures::REFERENCE_MODULE_FORMATION_ANGLAIS_AVANCE,
            ModuleFormationFixtures::REFERENCE_MODULE_FORMATION_TRADING,
            ModuleFormationFixtures::REFERENCE_MODULE_FORMATION_COMMUNICATION,
        ];

        foreach (EtablissementFixtures::LISTE_REFERENCES_ETABLISSEMENT as $etablissementReference) {
            $etablissement = $this->getReference($etablissementReference);
            $nbUtilisateurAlea = $this->faker->numberBetween(3, 5);

            for ($i = 0; $i < $nbUtilisateurAlea; $i++) {
                $nom = $this->faker->unique()->lastName;
                $prenom = $this->faker->unique()->firstName;
                $email = strtolower($prenom) . '.' . strtolower($nom) . '@dispostable.com';

                $utilisateur = new Utilisateur();
                $utilisateur->setNom($nom)
                    ->setPrenom($prenom)
                    ->setRoles(['ROLE_USER'])
                    ->setEmail($email)
                    ->setPassword($this->passwordHasher->hashPassword($utilisateur, 'Azerty123*'))
                    ->setEtablissement($etablissement)
                ;

                shuffle($moduleReferences); // Mélanger les références des modules
                $nbModules = $this->faker->numberBetween(1, 3);
                for ($j = 0; $j < $nbModules; $j++) {
                    $utilisateur->addModuleFormation($this->getReference($moduleReferences[$j]));
                }
                $manager->persist($utilisateur);
            }
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
