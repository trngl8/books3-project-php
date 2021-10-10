<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211010135731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE subscriptions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE subscriptions (id INT NOT NULL, uuid UUID NOT NULL, type VARCHAR(64) NOT NULL, external_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN subscriptions.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE slots ALTER start_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE slots ALTER start_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN slots.start_at IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE subscriptions_id_seq CASCADE');
        $this->addSql('DROP TABLE subscriptions');
        $this->addSql('ALTER TABLE slots ALTER start_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE slots ALTER start_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN slots.start_at IS \'(DC2Type:datetime_immutable)\'');
    }
}