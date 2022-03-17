<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220316193328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создает таблицу с модулями для генерации статей';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE module_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE module (
                 id INT NOT NULL,
                 client_id INT DEFAULT NULL, 
                 name VARCHAR(255) NOT NULL, 
                 body TEXT NOT NULL, 
                 created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                 updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                 deleted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                 PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_C24262819EB6921 ON module (client_id)');
        $this->addSql(
            'ALTER TABLE module 
                 ADD CONSTRAINT FK_C24262819EB6921 FOREIGN KEY (client_id) 
                     REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE module_id_seq CASCADE');
        $this->addSql('DROP TABLE module');
    }
}
