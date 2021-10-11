<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211011084451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profiles ADD emoji VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE profiles ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE profiles ADD active BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profiles DROP emoji');
        $this->addSql('ALTER TABLE profiles DROP status');
        $this->addSql('ALTER TABLE profiles DROP active');
    }
}