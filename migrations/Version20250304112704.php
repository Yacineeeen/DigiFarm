<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304112704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement (id INT AUTO_INCREMENT NOT NULL, idc INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, numero INT NOT NULL, typeabb VARCHAR(255) NOT NULL, dureeabb INT NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE animal (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, nom VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, age INT NOT NULL, poids DOUBLE PRECISION NOT NULL, race VARCHAR(255) DEFAULT NULL, INDEX IDX_6AAB231F79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE culture (id INT AUTO_INCREMENT NOT NULL, id_user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, surface DOUBLE PRECISION NOT NULL, date_plantation DATE NOT NULL, date_recolte DATE NOT NULL, region VARCHAR(255) NOT NULL, type_culture VARCHAR(25) NOT NULL, densite_plantation DOUBLE PRECISION NOT NULL, besoins_eau DOUBLE PRECISION NOT NULL, besoins_engrais VARCHAR(255) NOT NULL, rendement_moyen DOUBLE PRECISION NOT NULL, cout_moyen DOUBLE PRECISION NOT NULL, INDEX IDX_B6A99CEB79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etude (id INT AUTO_INCREMENT NOT NULL, culture_id INT NOT NULL, expert_id INT NOT NULL, id_user_id INT DEFAULT NULL, date_r DATE NOT NULL, climat VARCHAR(255) NOT NULL, type_sol VARCHAR(255) NOT NULL, irrigation TINYINT(1) NOT NULL, fertilisation TINYINT(1) NOT NULL, prix DOUBLE PRECISION NOT NULL, rendement DOUBLE PRECISION NOT NULL, precipitations DOUBLE PRECISION NOT NULL, main_oeuvre DOUBLE PRECISION NOT NULL, INDEX IDX_1DDEA924B108249D (culture_id), INDEX IDX_1DDEA924C5568CE4 (expert_id), INDEX IDX_1DDEA92479F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expert (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, tel INT NOT NULL, email VARCHAR(255) NOT NULL, zone VARCHAR(25) NOT NULL, dispo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, abonnement_id INT NOT NULL, datef DATE NOT NULL, prixt DOUBLE PRECISION NOT NULL, cin INT NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_FE866410F1D74413 (abonnement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE machine (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, date_achat DATE NOT NULL, etat_pred VARCHAR(255) DEFAULT NULL, etat VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintenance (id INT AUTO_INCREMENT NOT NULL, id_machine_id INT NOT NULL, id_technicien_id INT NOT NULL, date_entretien DATE NOT NULL, cout DOUBLE PRECISION NOT NULL, temperature INT DEFAULT NULL, humidite INT DEFAULT NULL, conso_carburant DOUBLE PRECISION DEFAULT NULL, conso_energie DOUBLE PRECISION DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_2F84F8E9533DDBF1 (id_machine_id), INDEX IDX_2F84F8E9AD6DA333 (id_technicien_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suivi (id INT AUTO_INCREMENT NOT NULL, id_animal INT DEFAULT NULL, veterinaire_id INT NOT NULL, id_user_id INT NOT NULL, temperature DOUBLE PRECISION DEFAULT NULL, rythme_cardiaque DOUBLE PRECISION DEFAULT NULL, etat VARCHAR(255) NOT NULL, id_client INT DEFAULT NULL, UNIQUE INDEX UNIQ_2EBCCA8F4C9C96F2 (id_animal), INDEX IDX_2EBCCA8F5C80924 (veterinaire_id), INDEX IDX_2EBCCA8F79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE technicien (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, specialite VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone INT NOT NULL, localisation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, adresse_mail VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE veterinaire (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, num_tel INT NOT NULL, email VARCHAR(255) NOT NULL, adresse_cabine VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE culture ADD CONSTRAINT FK_B6A99CEB79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE etude ADD CONSTRAINT FK_1DDEA924B108249D FOREIGN KEY (culture_id) REFERENCES culture (id)');
        $this->addSql('ALTER TABLE etude ADD CONSTRAINT FK_1DDEA924C5568CE4 FOREIGN KEY (expert_id) REFERENCES expert (id)');
        $this->addSql('ALTER TABLE etude ADD CONSTRAINT FK_1DDEA92479F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410F1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id)');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E9533DDBF1 FOREIGN KEY (id_machine_id) REFERENCES machine (id)');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E9AD6DA333 FOREIGN KEY (id_technicien_id) REFERENCES technicien (id)');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F4C9C96F2 FOREIGN KEY (id_animal) REFERENCES animal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F5C80924 FOREIGN KEY (veterinaire_id) REFERENCES veterinaire (id)');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F79F37AE5');
        $this->addSql('ALTER TABLE culture DROP FOREIGN KEY FK_B6A99CEB79F37AE5');
        $this->addSql('ALTER TABLE etude DROP FOREIGN KEY FK_1DDEA924B108249D');
        $this->addSql('ALTER TABLE etude DROP FOREIGN KEY FK_1DDEA924C5568CE4');
        $this->addSql('ALTER TABLE etude DROP FOREIGN KEY FK_1DDEA92479F37AE5');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410F1D74413');
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E9533DDBF1');
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E9AD6DA333');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F4C9C96F2');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F5C80924');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F79F37AE5');
        $this->addSql('DROP TABLE abonnement');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE culture');
        $this->addSql('DROP TABLE etude');
        $this->addSql('DROP TABLE expert');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE machine');
        $this->addSql('DROP TABLE maintenance');
        $this->addSql('DROP TABLE suivi');
        $this->addSql('DROP TABLE technicien');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE veterinaire');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
