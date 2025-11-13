<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112210307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item ADD created_by_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql(<<<'SQL'
            ALTER TABLE
              item
            ADD
              CONSTRAINT FK_1F1B251EB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)
        SQL);
        $this->addSql('CREATE INDEX IDX_1F1B251EB03A8386 ON item (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EB03A8386');
        $this->addSql('DROP INDEX IDX_1F1B251EB03A8386 ON item');
        $this->addSql('ALTER TABLE item DROP created_by_id');
    }
}
