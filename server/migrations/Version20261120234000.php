<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261120234000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add import history and customer SAP identifiers.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ImportRuns (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, kind VARCHAR(30) NOT NULL, source VARCHAR(255) NOT NULL, imported_at DATETIME NOT NULL, success BOOLEAN NOT NULL, created_count INTEGER NOT NULL, updated_count INTEGER NOT NULL, error_count INTEGER NOT NULL, message CLOB DEFAULT NULL, errors CLOB NOT NULL)');
        $this->addSql('ALTER TABLE Customers ADD COLUMN CustomersSapId VARCHAR(50) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E0A2CC8276A12393 ON Customers (CustomersSapId)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ImportRuns');
        $this->addSql('DROP INDEX UNIQ_E0A2CC8276A12393');
        $this->addSql('ALTER TABLE Customers DROP COLUMN CustomersSapId');
    }
}
