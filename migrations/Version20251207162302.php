<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251207162302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE flag (
              id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)',
              review_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid)',
              image VARCHAR(1024) DEFAULT NULL,
              UNIQUE INDEX UNIQ_D1F4EB9A3E2E969B (review_id),
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              flag
            ADD
              CONSTRAINT FK_D1F4EB9A3E2E969B FOREIGN KEY (review_id) REFERENCES review (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flag DROP FOREIGN KEY FK_D1F4EB9A3E2E969B');
        $this->addSql('DROP TABLE flag');
    }
}
