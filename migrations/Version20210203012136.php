<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210203012136 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service CHANGE paginator_id paginator_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD email VARCHAR(255) NOT NULL, ADD number INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495E237E06 ON user (name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service CHANGE paginator_id paginator_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D6495E237E06 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP email, DROP number');
    }
}
