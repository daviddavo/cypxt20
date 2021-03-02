<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302174144 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE online_call ADD COLUMN from_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__online_call AS SELECT id, name, age, type, number, comment, hours FROM online_call');
        $this->addSql('DROP TABLE online_call');
        $this->addSql('CREATE TABLE online_call (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, age SMALLINT DEFAULT NULL, type SMALLINT DEFAULT NULL, number VARCHAR(15) NOT NULL, comment CLOB DEFAULT NULL, hours CLOB DEFAULT NULL --(DC2Type:simple_array)
        )');
        $this->addSql('INSERT INTO online_call (id, name, age, type, number, comment, hours) SELECT id, name, age, type, number, comment, hours FROM __temp__online_call');
        $this->addSql('DROP TABLE __temp__online_call');
    }
}
