<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306115601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE machine ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE machine ADD CONSTRAINT FK_1505DF847E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1505DF847E3C61F9 ON machine (owner_id)');
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E9AD6DA333');
        $this->addSql('ALTER TABLE maintenance ADD etat_pred VARCHAR(255) DEFAULT NULL, CHANGE id_technicien_id id_technicien_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E9AD6DA333 FOREIGN KEY (id_technicien_id) REFERENCES technicien (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE technicien ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE machine DROP FOREIGN KEY FK_1505DF847E3C61F9');
        $this->addSql('DROP INDEX IDX_1505DF847E3C61F9 ON machine');
        $this->addSql('ALTER TABLE machine DROP owner_id');
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E9AD6DA333');
        $this->addSql('ALTER TABLE maintenance DROP etat_pred, CHANGE id_technicien_id id_technicien_id INT NOT NULL');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E9AD6DA333 FOREIGN KEY (id_technicien_id) REFERENCES technicien (id)');
        $this->addSql('ALTER TABLE technicien DROP latitude, DROP longitude');
    }
}
