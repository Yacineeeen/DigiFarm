<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306152458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F79F37AE5');
        $this->addSql('DROP INDEX IDX_6AAB231F79F37AE5 ON animal');
        $this->addSql('ALTER TABLE animal DROP id_user_id');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F79F37AE5');
        $this->addSql('DROP INDEX IDX_2EBCCA8F79F37AE5 ON suivi');
        $this->addSql('ALTER TABLE suivi ADD analysis LONGTEXT DEFAULT NULL, DROP id_user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal ADD id_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6AAB231F79F37AE5 ON animal (id_user_id)');
        $this->addSql('ALTER TABLE suivi ADD id_user_id INT NOT NULL, DROP analysis');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2EBCCA8F79F37AE5 ON suivi (id_user_id)');
    }
}
