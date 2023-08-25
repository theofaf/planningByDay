<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230823212616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[RENAME] Changement propriété id_abonnement_id en abonnement_id dans Etablissement';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etablissement DROP FOREIGN KEY FK_20FD592C4FFF9576');
        $this->addSql('DROP INDEX IDX_20FD592C4FFF9576 ON etablissement');
        $this->addSql('ALTER TABLE etablissement CHANGE id_abonnement_id abonnement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE etablissement ADD CONSTRAINT FK_20FD592CF1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id)');
        $this->addSql('CREATE INDEX IDX_20FD592CF1D74413 ON etablissement (abonnement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etablissement DROP FOREIGN KEY FK_20FD592CF1D74413');
        $this->addSql('DROP INDEX IDX_20FD592CF1D74413 ON etablissement');
        $this->addSql('ALTER TABLE etablissement CHANGE abonnement_id id_abonnement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE etablissement ADD CONSTRAINT FK_20FD592C4FFF9576 FOREIGN KEY (id_abonnement_id) REFERENCES abonnement (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_20FD592C4FFF9576 ON etablissement (id_abonnement_id)');
    }
}
