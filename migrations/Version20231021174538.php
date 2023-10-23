<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231021174538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "[AJOUT] Ajout d'une colonne 'est_lu' dans la table message";
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE message ADD est_lu TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE message DROP est_lu');
    }
}
