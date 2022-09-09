<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806115143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавляем информацию об ограничениях определенного типа подписки';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE subscription ADD block_time VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription ADD block_count INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE subscription DROP block_time');
        $this->addSql('ALTER TABLE subscription DROP block_count');
    }
}
