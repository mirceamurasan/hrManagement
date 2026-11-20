<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261120232000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add team and work location management.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE Locations (isTrashed BOOLEAN DEFAULT 0 NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, LocationsName VARCHAR(25) NOT NULL, LocationsAddress VARCHAR(255) DEFAULT NULL, LocationsCreated DATETIME NOT NULL, LocationsUpdated DATETIME DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9517C819C95AED88 ON Locations (LocationsName)');
        $this->addSql('CREATE TABLE Teams (isTrashed BOOLEAN DEFAULT 0 NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, TeamName VARCHAR(50) NOT NULL, TeamsCreated DATETIME NOT NULL, TeamsUpdated DATETIME DEFAULT NULL, TeamsLocation INTEGER DEFAULT NULL, CONSTRAINT FK_57030D5CC8110DA1 FOREIGN KEY (TeamsLocation) REFERENCES Locations (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57030D5C109F2030 ON Teams (TeamName)');
        $this->addSql('CREATE INDEX IDX_57030D5CC8110DA1 ON Teams (TeamsLocation)');
        $this->addSql('ALTER TABLE Workers ADD COLUMN WorkersTeam INTEGER DEFAULT NULL REFERENCES Teams (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE Workers ADD COLUMN WorkersLocation INTEGER DEFAULT NULL REFERENCES Locations (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_7790445C2DBAF2A2 ON Workers (WorkersTeam)');
        $this->addSql('CREATE INDEX IDX_7790445C43B176E2 ON Workers (WorkersLocation)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_7790445C2DBAF2A2');
        $this->addSql('DROP INDEX IDX_7790445C43B176E2');
        $this->addSql('ALTER TABLE Workers DROP COLUMN WorkersTeam');
        $this->addSql('ALTER TABLE Workers DROP COLUMN WorkersLocation');
        $this->addSql('DROP TABLE Teams');
        $this->addSql('DROP TABLE Locations');
    }
}
