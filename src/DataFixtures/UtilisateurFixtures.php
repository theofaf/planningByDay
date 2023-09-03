<?php

namespace App\DataFixtures;

use App\Entity\Etablissement;
use App\Entity\Message;
use App\Entity\ModuleFormation;
use App\Entity\ModuleFormationUtilisateur;
use App\Entity\Statut;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        $moduleReferences = ModuleFormationFixtures::LISTE_FORMATIONS_MODULES;

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
                    ->setDateEnvoi($this->faker->dateTimeBetween(startDate: '-1 year'))
                    ->setContenu($this->faker->realText(100))
                    ->setStatut($this->getReference(
                        $this->faker->randomElement(Statut::LISTE_STATUT_MESSAGE),
                        Statut::class
                    )
                );

                $ticket = (new Ticket())
                    ->setUtilisateur($utilisateursMemeEtablissement[0])
                    ->setEtablissement($etablissement)
                    ->setStatut($this->getReference(
                        $this->faker->randomElement(Statut::LISTE_STATUT_MESSAGE),
                        Statut::class,
                    ))
                    ->setSujet($this->faker->words(5, true))
                    ->setMessage($this->faker->realText(300))
                    ->setDateEnvoi($this->faker->dateTimeBetween(startDate: '-1 year'))
                ;

                $utilisateursMemeEtablissement[0]->addMessage($message);
                $utilisateursMemeEtablissement[0]->addTicket($ticket);
                $manager->persist($message);
                $manager->persist($ticket);
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
