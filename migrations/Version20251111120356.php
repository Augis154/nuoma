<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251111120356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE
              review
            ADD
              item_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)',
            ADD
              created_by_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              review
            ADD
              CONSTRAINT FK_794381C6126F525E FOREIGN KEY (item_id) REFERENCES item (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              review
            ADD
              CONSTRAINT FK_794381C6B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)
        SQL);
        $this->addSql('CREATE INDEX IDX_794381C6126F525E ON review (item_id)');
        $this->addSql('CREATE INDEX IDX_794381C6B03A8386 ON review (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6126F525E');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6B03A8386');
        $this->addSql('DROP INDEX IDX_794381C6126F525E ON review');
        $this->addSql('DROP INDEX IDX_794381C6B03A8386 ON review');
        $this->addSql('ALTER TABLE review DROP item_id, DROP created_by_id');
    }
}
