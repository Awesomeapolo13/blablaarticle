<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220905135950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавляет поле для url картинки';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article_image ADD url TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article_image DROP url');
    }
}
