<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220101230815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, method VARCHAR(255) NOT NULL, c_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE todo_list (id INT AUTO_INCREMENT NOT NULL, todo_id VARCHAR(255) NOT NULL, level INT NOT NULL, estimated INT NOT NULL, hash VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql("INSERT INTO provider (`id`, `url`, `method`, `c_name`) VALUES (1, 'http://www.mocky.io/v2/5d47f24c330000623fa3ebfa', 'GET', 'Provider1Adapter');");
        $this->addSql("INSERT INTO provider (`id`, `url`, `method`, `c_name`) VALUES (2, 'http://www.mocky.io/v2/5d47f235330000623fa3ebf7', 'GET', 'Provider2Adapter');");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE provider');
        $this->addSql('DROP TABLE todo_list');
    }
}
