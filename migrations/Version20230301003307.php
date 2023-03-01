<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230301003307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE day_generation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE day_generation (id INT NOT NULL, client_id INT NOT NULL, generation VARCHAR(10) NOT NULL, date DATE NOT NULL, hours VARCHAR(5) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F5A6ECE419EB6921 ON day_generation (client_id)');
        $this->addSql('ALTER TABLE day_generation ADD CONSTRAINT FK_F5A6ECE419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE day_generation_id_seq CASCADE');
        $this->addSql('ALTER TABLE day_generation DROP CONSTRAINT FK_F5A6ECE419EB6921');
        $this->addSql('DROP TABLE day_generation');
    }
}
