<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231022182641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Ajout] ajout colonne statut_id dans la table session';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO statut VALUES (6, 'en attente', 'en_attente')");
        $this->addSql('ALTER TABLE session ADD statut_id INT NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4F6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)');
        $this->addSql('CREATE INDEX IDX_D044D5D4F6203804 ON session (statut_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM statut where id=6");
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4F6203804');
        $this->addSql('DROP INDEX IDX_D044D5D4F6203804 ON session');
        $this->addSql('ALTER TABLE session DROP statut_id');
    }
}
