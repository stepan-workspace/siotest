<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230817230247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(2) NOT NULL, UNIQUE INDEX UNIQ_5373C9665E237E06 (name), UNIQUE INDEX unique_code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country_tax (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, value INT NOT NULL, rule VARCHAR(255) NOT NULL, INDEX IDX_B5A98CE7F92F3E70 (country_id), UNIQUE INDEX unique_country_id_rule (country_id, rule), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(7, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE country_tax ADD CONSTRAINT FK_B5A98CE7F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country_tax DROP FOREIGN KEY FK_B5A98CE7F92F3E70');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE country_tax');
        $this->addSql('DROP TABLE product');
    }
}
