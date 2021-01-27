<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210121033301 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paginator (id INT AUTO_INCREMENT NOT NULL, page INT NOT NULL, nb_page INT NOT NULL, name_route VARCHAR(255) NOT NULL, params_route LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service ADD paginator_id INT NOT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD236790833 FOREIGN KEY (paginator_id) REFERENCES paginator (id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD236790833 ON service (paginator_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD236790833');
        $this->addSql('DROP TABLE paginator');
        $this->addSql('DROP INDEX IDX_E19D9AD236790833 ON service');
        $this->addSql('ALTER TABLE service DROP paginator_id');
    }
}
