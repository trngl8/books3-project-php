<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211011120527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscriptions ADD profile_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A01CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profiles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4778A01CCFA12B8 ON subscriptions (profile_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscriptions DROP CONSTRAINT FK_4778A01CCFA12B8');
        $this->addSql('DROP INDEX IDX_4778A01CCFA12B8');
        $this->addSql('ALTER TABLE subscriptions DROP profile_id');
    }
}