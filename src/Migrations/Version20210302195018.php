<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302195018 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE cypxt_line (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, description CLOB DEFAULT NULL, status VARCHAR(31) NOT NULL, last_open DATETIME NOT NULL, last_close DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE cypxt_number (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, phone_number VARCHAR(31) NOT NULL, type VARCHAR(7) NOT NULL)');
        $this->addSql('CREATE TABLE cypxt_online_call (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, age SMALLINT DEFAULT NULL, type SMALLINT DEFAULT NULL, number VARCHAR(15) NOT NULL, comment CLOB DEFAULT NULL, hours CLOB DEFAULT NULL --(DC2Type:simple_array)
        , from_name VARCHAR(255) NOT NULL)');
        $this->addSql('DROP TABLE line');
        $this->addSql('DROP TABLE number');
        $this->addSql('DROP TABLE online_call');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE line (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, description CLOB DEFAULT NULL COLLATE BINARY, status VARCHAR(31) NOT NULL COLLATE BINARY, last_open DATETIME NOT NULL, last_close DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE number (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, phone_number VARCHAR(31) NOT NULL COLLATE BINARY, type VARCHAR(7) NOT NULL COLLATE BINARY)');
        $this->addSql('CREATE TABLE online_call (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, age SMALLINT DEFAULT NULL, type SMALLINT DEFAULT NULL, number VARCHAR(15) NOT NULL COLLATE BINARY, comment CLOB DEFAULT NULL COLLATE BINARY, hours CLOB DEFAULT NULL COLLATE BINARY --(DC2Type:simple_array)
        , from_name VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('DROP TABLE cypxt_line');
        $this->addSql('DROP TABLE cypxt_number');
        $this->addSql('DROP TABLE cypxt_online_call');
    }
}
