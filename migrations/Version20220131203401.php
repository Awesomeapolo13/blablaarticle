<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220131203401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создает таблицу для информации о существующих подписках';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE subscription (
                 id INT NOT NULL, 
                 name VARCHAR(255) NOT NULL, 
                 price NUMERIC(10, 2) NOT NULL, 
                 opportunities TEXT NOT NULL, 
                 created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                 updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                 PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN subscription.opportunities IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE subscription_id_seq CASCADE');
        $this->addSql('DROP TABLE subscription');
    }
}
