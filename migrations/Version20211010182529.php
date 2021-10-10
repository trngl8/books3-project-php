<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211010182529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slots DROP CONSTRAINT fk_ac0e20677e3c61f9');
        $this->addSql('ALTER TABLE slots ADD CONSTRAINT FK_C87435D07E3C61F9 FOREIGN KEY (owner_id) REFERENCES profiles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slots DROP CONSTRAINT FK_C87435D07E3C61F9');
        $this->addSql('ALTER TABLE slots ADD CONSTRAINT fk_ac0e20677e3c61f9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}