<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220301202426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создает таблицу для хранения api-токенов пользователей';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE api_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE api_token (
                 id INT NOT NULL, 
                 client_id INT NOT NULL, 
                 token VARCHAR(255) NOT NULL, 
                 expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                 created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                 updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                 PRIMARY KEY(id)
                       )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BA2F5EB19EB6921 ON api_token (client_id)');
        $this->addSql(
            'ALTER TABLE api_token 
                 ADD CONSTRAINT FK_7BA2F5EB19EB6921 
                 FOREIGN KEY (client_id) 
                     REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE api_token_id_seq CASCADE');
        $this->addSql('DROP TABLE api_token');
    }
}
