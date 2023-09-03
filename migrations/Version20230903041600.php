<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Statut;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230903041600 extends AbstractMigration
{
    public const DONNEES_STATUT = Statut::LISTE_STATUT_MESSAGE;
    public const DONNEES_ABONNEMENT = [
        [
            'libelle' => 'Essentielle',
            'libelleTechnique' => 'essentielle',
            'prix' => 5000,
        ],
        [
            'libelle' => 'Avancée',
            'libelleTechnique' => 'avancee',
            'prix' => 7500,
        ],
        [
            'libelle' => 'Premium',
            'libelleTechnique' => 'premium',
            'prix' => 10000,
        ],
    ];

    public function getDescription(): string
    {
        return "
            [AJOUT DONNEES ET CHAMPS] 
            - Ajout des données des tables 'abonnement' et 'statut'
            - Ajout d'une colonne 'libelle_technique' dans 'statut' et 'abonnement'
        ";
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE statut ADD libelle_technique VARCHAR(15) NOT NULL');
        $this->addSql('ALTER TABLE abonnement ADD libelle_technique VARCHAR(20) NOT NULL AFTER libelle');

        // this up() migration is auto-generated, please modify it to your needs
        foreach (self::DONNEES_ABONNEMENT as $data) {
            $this->addSql("INSERT INTO abonnement(libelle, libelle_technique, prix) VALUES(:libelle, :libelleTechnique, :prix)", $data);
        }

        foreach (self::DONNEES_STATUT as $technique => $francise) {
            $this->addSql("INSERT INTO statut(libelle, libelle_technique) 
                VALUES(:libelle, :libelle_technique)", ['libelle' => $francise, 'libelle_technique' => $technique]
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        foreach (self::DONNEES_ABONNEMENT as $data) {
            $this->addSql("DELETE FROM abonnement where prix = :prix", $data);
        }

        foreach (self::DONNEES_STATUT as $data) {
            $this->addSql("DELETE FROM statut where libelle = :libelle", ['libelle' => $data]);
        }
        $this->addSql('ALTER TABLE statut DROP COLUMN libelle_technique');
        $this->addSql('ALTER TABLE abonnement DROP COLUMN libelle_technique');
    }
}
