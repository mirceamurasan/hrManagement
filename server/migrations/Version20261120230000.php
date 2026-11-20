<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261120230000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add contract periods with date range and rate details.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE Periods (isTrashed BOOLEAN DEFAULT 0 NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, startdate DATE NOT NULL, enddate DATE DEFAULT NULL, tjm INTEGER DEFAULT NULL, serviceProvider BOOLEAN NOT NULL, PeriodsCreated DATETIME NOT NULL, idcontract INTEGER NOT NULL, CONSTRAINT FK_A8AAA13E435A6054 FOREIGN KEY (idcontract) REFERENCES Contracts (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A8AAA13E435A6054 ON Periods (idcontract)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE Periods');
    }
}
