<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211009181252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE slots_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE slots (id INT NOT NULL, owner_id INT DEFAULT NULL, uuid UUID NOT NULL, start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, duration INT NOT NULL, status VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC0E20677E3C61F9 ON slots (owner_id)');
        $this->addSql('COMMENT ON COLUMN slots.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN slots.start_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE slots ADD CONSTRAINT FK_AC0E20677E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE slots_id_seq CASCADE');
        $this->addSql('DROP TABLE slots');
    }
}