<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210607192339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE orders (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN orders.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE order_card (order_id UUID NOT NULL, card_id UUID NOT NULL, PRIMARY KEY(order_id, card_id))');
        $this->addSql('CREATE INDEX IDX_5BE5A2C48D9F6D38 ON order_card (order_id)');
        $this->addSql('CREATE INDEX IDX_5BE5A2C44ACC9A20 ON order_card (card_id)');
        $this->addSql('COMMENT ON COLUMN order_card.order_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_card.card_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE order_card ADD CONSTRAINT FK_5BE5A2C48D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_card ADD CONSTRAINT FK_5BE5A2C44ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_card DROP CONSTRAINT FK_5BE5A2C48D9F6D38');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE order_card');
    }
}