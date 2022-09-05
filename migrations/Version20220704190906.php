<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704190906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создает таблицу изображений для статей и добавляет ей связь с таблицей статей';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE article_image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE article_image (
                 id INT NOT NULL, 
                 article_id INT NOT NULL, 
                 name VARCHAR(255) DEFAULT NULL, 
                 PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_B28A764E7294869C ON article_image (article_id)');
        $this->addSql(
            'ALTER TABLE article_image 
                 ADD CONSTRAINT FK_B28A764E7294869C FOREIGN KEY (article_id) 
                 REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE article_image_id_seq CASCADE');
        $this->addSql('DROP TABLE article_image');
    }
}
