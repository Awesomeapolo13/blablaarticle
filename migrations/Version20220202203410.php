<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220202203410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавлена связь таблицы пользователей с подписками';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD subscription_id INT NOT NULL, ADD expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6499A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D6499A1887DC ON "user" (subscription_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6499A1887DC');
        $this->addSql('DROP INDEX IDX_8D93D6499A1887DC');
        $this->addSql('ALTER TABLE "user" DROP subscription_id, DROP expire_at');
    }
}
