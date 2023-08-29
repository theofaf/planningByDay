<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230825195624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "
            [MODIFICATION ENTITES] Plusieurs ajouts :
            - Ajout d'une date pour la table message
            - Ajout d'une date et d'un bool pour module_formation_utilisateur afin de vérifier si un formateur peut enseigner un cours
            - Ajout d'une date dans utilisateur, afin d'indiquer si le token d'auth doit expiré
        ";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD date_envoi DATE NOT NULL');
        $this->addSql('ALTER TABLE module_formation_utilisateur DROP FOREIGN KEY FK_F01E792D3A53B0DC');
        $this->addSql('ALTER TABLE module_formation_utilisateur DROP FOREIGN KEY FK_F01E792DFB88E14F');
        $this->addSql('ALTER TABLE module_formation_utilisateur ADD date_derniere_session DATETIME NOT NULL, ADD competence_active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE module_formation_utilisateur ADD CONSTRAINT FK_F01E792D3A53B0DC FOREIGN KEY (module_formation_id) REFERENCES module_formation (id)');
        $this->addSql('ALTER TABLE module_formation_utilisateur ADD CONSTRAINT FK_F01E792DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE utilisateur ADD date_derniere_action DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP date_envoi');
        $this->addSql('ALTER TABLE utilisateur DROP date_derniere_action');
        $this->addSql('ALTER TABLE module_formation_utilisateur DROP FOREIGN KEY FK_F01E792D3A53B0DC');
        $this->addSql('ALTER TABLE module_formation_utilisateur DROP FOREIGN KEY FK_F01E792DFB88E14F');
        $this->addSql('ALTER TABLE module_formation_utilisateur DROP date_derniere_session, DROP competence_active');
        $this->addSql('ALTER TABLE module_formation_utilisateur ADD CONSTRAINT FK_F01E792D3A53B0DC FOREIGN KEY (module_formation_id) REFERENCES module_formation (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE module_formation_utilisateur ADD CONSTRAINT FK_F01E792DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
