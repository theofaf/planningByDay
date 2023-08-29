<?php

namespace App\DataFixtures;

use App\Entity\Etablissement;
use App\Entity\Message;
use App\Entity\ModuleFormation;
use App\Entity\ModuleFormationUtilisateur;
use App\Entity\Statut;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Faker\Generator;

class UtilisateurFixtures extends Fixture implements DependentFixtureInterface
{
    private ?Generator $faker;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UtilisateurRepository $utilisateurRepository,
    ) {
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
            /** @var Etablissement $etablissement */
            $etablissement = $this->getReference($etablissementReference);
            $nbUtilisateurAlea = $this->faker->numberBetween(3, 10);

            for ($i = 0; $i < $nbUtilisateurAlea; $i++) {
                $nom = $this->faker->unique()->lastName;
                $prenom = $this->faker->unique()->firstName;
                $email = strtolower($prenom) . '.' . strtolower($nom) . '@dispostable.com';

                $utilisateur = new Utilisateur();
                $utilisateur
                    ->setNom($nom)
                    ->setPrenom($prenom)
                    ->setRoles(['ROLE_USER'])
                    ->setEmail($email)
                    ->setPassword($this->passwordHasher->hashPassword($utilisateur, 'Azerty123*'))
                    ->setEtablissement($etablissement)
                    ->setDateDerniereAction(new DateTime())
                ;

                shuffle($moduleReferences);
                $nbModules = $this->faker->numberBetween(1, 3);
                for ($j = 0; $j < $nbModules; $j++) {
                    /** @var ModuleFormation $moduleFormation */
                    $moduleFormation = $this->getReference($moduleReferences[$j]);
                    $moduleFormationUtilisateur = (new ModuleFormationUtilisateur())
                        ->setCompetenceActive(1)
                        ->setUtilisateur($utilisateur)
                        ->setDateDerniereSession(new DateTime())
                        ->setModuleFormation($moduleFormation)
                    ;
                    $utilisateur->addListeModulesFormation($moduleFormationUtilisateur);
                    $moduleFormation->addModuleFormationUtilisateur($moduleFormationUtilisateur);
                }

                $manager->persist($utilisateur);
            }

            $manager->flush();

            for ($k = 0; $k <= $this->faker->numberBetween(3, 5); $k++) {
                $utilisateursMemeEtablissement = $this->utilisateurRepository
                    ->recupererUtilisateursMemeEtablissement($etablissement)
                ;
                $utilisateursMemeEtablissement = $this->faker->randomElements($utilisateursMemeEtablissement, 2);
                $message = (new Message())
                    ->setEmetteur($utilisateursMemeEtablissement[0])
                    ->setReceveur($utilisateursMemeEtablissement[1])
                    ->setDateEnvoi($this->faker->dateTimeBetween())
                    ->setContenu($this->faker->realText(100))
                    ->setStatut($this->getReference(
                        $this->faker->randomElement(Statut::LISTE_STATUT_MESSAGE),
                        Statut::class
                    )
                );

                $utilisateursMemeEtablissement[0]->addMessage($message);
                $manager->persist($message);
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
