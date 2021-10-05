<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211005181800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE order_items_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE order_items (id INT NOT NULL, card_id UUID DEFAULT NULL, purchase_id UUID DEFAULT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_62809DB04ACC9A20 ON order_items (card_id)');
        $this->addSql('CREATE INDEX IDX_62809DB0558FBEB9 ON order_items (purchase_id)');
        $this->addSql('COMMENT ON COLUMN order_items.card_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_items.purchase_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB04ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0558FBEB9 FOREIGN KEY (purchase_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE order_card');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE order_items_id_seq CASCADE');
        $this->addSql('CREATE TABLE order_card (order_id UUID NOT NULL, card_id UUID NOT NULL, PRIMARY KEY(order_id, card_id))');
        $this->addSql('CREATE INDEX idx_5be5a2c44acc9a20 ON order_card (card_id)');
        $this->addSql('CREATE INDEX idx_5be5a2c48d9f6d38 ON order_card (order_id)');
        $this->addSql('COMMENT ON COLUMN order_card.order_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_card.card_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE order_card ADD CONSTRAINT fk_5be5a2c48d9f6d38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_card ADD CONSTRAINT fk_5be5a2c44acc9a20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE order_items');
    }
}