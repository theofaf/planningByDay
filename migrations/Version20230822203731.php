<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230822203731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[INIT BDD] initialisation de base de données';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(20) NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE batiment (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT NOT NULL, libelle VARCHAR(30) NOT NULL, num_voie INT NOT NULL, rue VARCHAR(30) NOT NULL, ville VARCHAR(30) NOT NULL, code_postal INT NOT NULL, numero_tel VARCHAR(255) DEFAULT NULL, INDEX IDX_F5FAB00CFF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, cursus_id INT NOT NULL, libelle VARCHAR(30) NOT NULL, nombre_eleves INT NOT NULL, INDEX IDX_8F87BF9640AEF4B9 (cursus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cursus (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etablissement (id INT AUTO_INCREMENT NOT NULL, id_abonnement_id INT DEFAULT NULL, libelle VARCHAR(30) NOT NULL, num_voie INT NOT NULL, rue VARCHAR(50) NOT NULL, ville VARCHAR(30) NOT NULL, code_postal INT NOT NULL, numero_tel VARCHAR(255) NOT NULL, statut_abonnement TINYINT(1) NOT NULL, date_abonnement DATE DEFAULT NULL, INDEX IDX_20FD592C4FFF9576 (id_abonnement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, statut_id INT NOT NULL, receveur_id INT NOT NULL, emetteur_id INT NOT NULL, contenu VARCHAR(255) NOT NULL, INDEX IDX_B6BD307FF6203804 (statut_id), INDEX IDX_B6BD307FB967E626 (receveur_id), INDEX IDX_B6BD307F79E92E8C (emetteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module_formation (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL, duree TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module_formation_cursus (module_formation_id INT NOT NULL, cursus_id INT NOT NULL, INDEX IDX_2FDC7E773A53B0DC (module_formation_id), INDEX IDX_2FDC7E7740AEF4B9 (cursus_id), PRIMARY KEY(module_formation_id, cursus_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module_formation_utilisateur (module_formation_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_F01E792D3A53B0DC (module_formation_id), INDEX IDX_F01E792DFB88E14F (utilisateur_id), PRIMARY KEY(module_formation_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, batiment_id INT NOT NULL, libelle VARCHAR(30) NOT NULL, nb_place INT NOT NULL, equipement_info TINYINT(1) NOT NULL, INDEX IDX_4E977E5CD6F6891B (batiment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, module_formation_id INT NOT NULL, utilisateur_id INT NOT NULL, classe_id INT NOT NULL, salle_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, INDEX IDX_D044D5D43A53B0DC (module_formation_id), INDEX IDX_D044D5D4FB88E14F (utilisateur_id), INDEX IDX_D044D5D48F5EA509 (classe_id), INDEX IDX_D044D5D4DC304035 (salle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, statut_id INT NOT NULL, utilisateur_id INT NOT NULL, etablissement_id INT NOT NULL, sujet VARCHAR(35) NOT NULL, message VARCHAR(255) NOT NULL, INDEX IDX_97A0ADA3F6203804 (statut_id), INDEX IDX_97A0ADA3FB88E14F (utilisateur_id), INDEX IDX_97A0ADA3FF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B3FF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE batiment ADD CONSTRAINT FK_F5FAB00CFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9640AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('ALTER TABLE etablissement ADD CONSTRAINT FK_20FD592C4FFF9576 FOREIGN KEY (id_abonnement_id) REFERENCES abonnement (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FB967E626 FOREIGN KEY (receveur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F79E92E8C FOREIGN KEY (emetteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE module_formation_cursus ADD CONSTRAINT FK_2FDC7E773A53B0DC FOREIGN KEY (module_formation_id) REFERENCES module_formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE module_formation_cursus ADD CONSTRAINT FK_2FDC7E7740AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE module_formation_utilisateur ADD CONSTRAINT FK_F01E792D3A53B0DC FOREIGN KEY (module_formation_id) REFERENCES module_formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE module_formation_utilisateur ADD CONSTRAINT FK_F01E792DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5CD6F6891B FOREIGN KEY (batiment_id) REFERENCES batiment (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D43A53B0DC FOREIGN KEY (module_formation_id) REFERENCES module_formation (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D48F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3F6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batiment DROP FOREIGN KEY FK_F5FAB00CFF631228');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF9640AEF4B9');
        $this->addSql('ALTER TABLE etablissement DROP FOREIGN KEY FK_20FD592C4FFF9576');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF6203804');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FB967E626');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F79E92E8C');
        $this->addSql('ALTER TABLE module_formation_cursus DROP FOREIGN KEY FK_2FDC7E773A53B0DC');
        $this->addSql('ALTER TABLE module_formation_cursus DROP FOREIGN KEY FK_2FDC7E7740AEF4B9');
        $this->addSql('ALTER TABLE module_formation_utilisateur DROP FOREIGN KEY FK_F01E792D3A53B0DC');
        $this->addSql('ALTER TABLE module_formation_utilisateur DROP FOREIGN KEY FK_F01E792DFB88E14F');
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5CD6F6891B');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D43A53B0DC');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4FB88E14F');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D48F5EA509');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4DC304035');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3F6203804');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3FB88E14F');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3FF631228');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3FF631228');
        $this->addSql('DROP TABLE abonnement');
        $this->addSql('DROP TABLE batiment');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE cursus');
        $this->addSql('DROP TABLE etablissement');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE module_formation');
        $this->addSql('DROP TABLE module_formation_cursus');
        $this->addSql('DROP TABLE module_formation_utilisateur');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE statut');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE utilisateur');
    }
}
