<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251202174710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE lease (
              id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)',
              item_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid)',
              lessee_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid)',
              returned TINYINT(1) NOT NULL,
              created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
              updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
              INDEX IDX_E6C77495126F525E (item_id),
              INDEX IDX_E6C7749550499E36 (lessee_id),
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              lease
            ADD
              CONSTRAINT FK_E6C77495126F525E FOREIGN KEY (item_id) REFERENCES item (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              lease
            ADD
              CONSTRAINT FK_E6C7749550499E36 FOREIGN KEY (lessee_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lease DROP FOREIGN KEY FK_E6C77495126F525E');
        $this->addSql('ALTER TABLE lease DROP FOREIGN KEY FK_E6C7749550499E36');
        $this->addSql('DROP TABLE lease');
    }
}
