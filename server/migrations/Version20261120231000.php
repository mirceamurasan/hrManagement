<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261120231000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add worker vacations attached to contract periods.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE Vacations (isTrashed BOOLEAN DEFAULT 0 NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, startdate DATE NOT NULL, enddate DATE NOT NULL, morning BOOLEAN NOT NULL, afternoon BOOLEAN NOT NULL, coef NUMERIC(2, 1) NOT NULL, VacationsCreated DATETIME NOT NULL, VacationPeriods INTEGER NOT NULL, CONSTRAINT FK_B97312C47AD847F8 FOREIGN KEY (VacationPeriods) REFERENCES Periods (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B97312C47AD847F8 ON Vacations (VacationPeriods)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE Vacations');
    }
}
