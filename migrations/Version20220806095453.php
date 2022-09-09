<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806095453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Устанавливает возможность создавать статьи без привязки к пользователю. Устанавливает флаг isDefault для модулей.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ALTER client_id SET NOT NULL');
        $this->addSql('ALTER TABLE module ADD is_default BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE module DROP is_default');
        $this->addSql('ALTER TABLE article ALTER client_id DROP NOT NULL');
    }
}
