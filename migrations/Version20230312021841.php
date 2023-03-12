<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230312021841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE bill_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bill (id INT NOT NULL, client_id INT NOT NULL, price VARCHAR(10) NOT NULL, actual_reading_date DATE NOT NULL, next_reading_date DATE NOT NULL, previous_reading_date DATE DEFAULT NULL, generation_balance VARCHAR(255) DEFAULT NULL, energy_consumed VARCHAR(255) NOT NULL, energy_excess VARCHAR(255) DEFAULT NULL, date_month SMALLINT NOT NULL, date_year SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7A2119E319EB6921 ON bill (client_id)');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E319EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE bill_id_seq CASCADE');
        $this->addSql('ALTER TABLE bill DROP CONSTRAINT FK_7A2119E319EB6921');
        $this->addSql('DROP TABLE bill');
    }
}
