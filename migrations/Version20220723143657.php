<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220723143657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавляет привязку статей к пользователям';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6619EB6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_23A0E6619EB6921 ON article (client_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E6619EB6921');
        $this->addSql('DROP INDEX IDX_23A0E6619EB6921');
        $this->addSql('ALTER TABLE article DROP client_id');
    }
}
