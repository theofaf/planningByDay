<?php

namespace App\DataFixtures;

use App\Entity\Classe;
use App\Entity\Cursus;
use App\Entity\ModuleFormation;
use App\Entity\Session;
use App\Entity\Utilisateur;
use App\Repository\BatimentRepository;
use App\Repository\SalleRepository;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class SessionFixtures extends Fixture implements DependentFixtureInterface
{
    private ?Generator $faker;

    public function __construct(
        private readonly SalleRepository $salleRepository,
        private readonly BatimentRepository $batimentRepository,
    ) {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($l = 0; $l < 5; $l++) {
            for ($m = 0; $m < 5; $m++) {
                $listeCursus = CursusFixtures::LISTE_REFERENCES_CURSUS;
                shuffle($listeCursus);
                /** @var Cursus $cursus */
                $cursus = $this->getReference($listeCursus[0]);

                $classes = $cursus->getClasses()->toArray();
                $modules = $cursus->getModuleFormations()->toArray();
                shuffle($modules);
                shuffle($classes);

                /** @var Classe $classe */
                $classe = (new ArrayCollection($classes))
                    ->filter(fn(Classe $c) => $c->getCursus()?->getId() === $cursus->getId()
                )->first();
                /** @var ModuleFormation $module */
                $module = $modules[0];

                $modulesParFormateurs = $module->getModuleFormationUtilisateurs()->toArray();
                shuffle($modulesParFormateurs);
                /** @var Utilisateur $formateur */
                $formateur = $modulesParFormateurs[0]->getUtilisateur();

                $etablissement = $formateur->getEtablissement();
                $batiments = $this->batimentRepository->findBy(['etablissement' => $etablissement->getId()]);
                shuffle($batiments);
                $batiment = $batiments[0];

                $salles = $this->salleRepository->recupererSallesAvecNbPlaceMini(
                    $classe->getNombreEleves(),
                    $batiment,
                );
                shuffle($salles);
                $salle = $salles[0];

                list($dateDebut, $dateFin) = $this->creerDatesSession();
                $session = (new Session())
                    ->setUtilisateur($formateur)
                    ->setModuleFormation($module)
                    ->setDateDebut($dateDebut)
                    ->setDateFin($dateFin)
                    ->setClasse($classe)
                    ->setSalle($salle)
                ;
                $manager->persist($session);
            }
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EtablissementFixtures::class,
            BatimentFixtures::class,
            SalleFixtures::class,
            ClasseFixtures::class,
            CursusFixtures::class,
            ModuleFormationFixtures::class,
            UtilisateurFixtures::class,
        ];
    }

    /**
     * Retourne un tableau contenant une paire de dates pour une session.
     * La date de début est générée de manière aléatoire entre aujourd'hui et un an en arrière,
     * avec un horaire entre 8h et 17h.
     * La date de fin est calculée en ajoutant 1 heure à la date de début.
     *
     * @return array<DateTime>
     */
    public function creerDatesSession(): array
    {
        $now = new DateTime();
        $lastYear = (clone $now)->modify('-1 year');
        $jour = mt_rand(0, $lastYear->diff($now)->days);
        $dateDebut = (clone $lastYear)->add(
            new DateInterval("P{$jour}D")
        )->setTime($this->faker->numberBetween(8, 17), 0);
        $dateFin = (clone $dateDebut)->modify('+1 hour');

        return [$dateDebut, $dateFin];
    }
}