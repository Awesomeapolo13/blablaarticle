<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220103175511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создает таблицу article для хранения результатов генерации статей';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE article (id INT NOT NULL, theme VARCHAR(100) NOT NULL, title VARCHAR(60) NOT NULL, size INT NOT NULL, promoted_words JSON DEFAULT NULL, body TEXT NOT NULL, images VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE article_id_seq CASCADE');
        $this->addSql('DROP TABLE article');
    }
}
