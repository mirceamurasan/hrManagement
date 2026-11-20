<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120194732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Jobs (isTrashed BOOLEAN DEFAULT 0 NOT NULL, Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, JobsLabel VARCHAR(45) DEFAULT NULL, JobsCreated DATETIME NOT NULL, JobsUpdated DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE Providers (isTrashed BOOLEAN DEFAULT 0 NOT NULL, Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ProvidersName VARCHAR(50) NOT NULL, ProvidersCreated DATETIME NOT NULL, ProvidersUpdated DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE States (isTrashed BOOLEAN DEFAULT 0 NOT NULL, Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, StatesLabel VARCHAR(45) DEFAULT NULL, StatesCreated DATETIME NOT NULL, StatesUpdated DATETIME DEFAULT NULL, StatesIsDefault BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE Tasks (isTrashed BOOLEAN DEFAULT 0 NOT NULL, Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, TasksName VARCHAR(45) NOT NULL, TasksBeginAt DATE NOT NULL, TasksEndAt DATE NOT NULL, TasksOrder SMALLINT NOT NULL, LotsIdLots INTEGER DEFAULT NULL, TasksCreatedBy INTEGER NOT NULL, TasksCreated DATETIME NOT NULL, TasksUpdated DATETIME DEFAULT NULL, ProjectsIdProjects INTEGER NOT NULL, StatesIdStates INTEGER NOT NULL, CONSTRAINT FK_91994A93890B443D FOREIGN KEY (ProjectsIdProjects) REFERENCES Projects (Id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_91994A933DF7D249 FOREIGN KEY (StatesIdStates) REFERENCES States (Id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_91994A93890B443D ON Tasks (ProjectsIdProjects)');
        $this->addSql('CREATE INDEX IDX_91994A933DF7D249 ON Tasks (StatesIdStates)');
        $this->addSql('CREATE TABLE TasksLoads (isTrashed BOOLEAN DEFAULT 0 NOT NULL, Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, TasksLoadsWorkers INTEGER DEFAULT NULL, TasksLoadsVolume SMALLINT NOT NULL, TasksLoadsCreatedBy INTEGER NOT NULL, TasksLoadsCreated DATETIME NOT NULL, TasksLoadsUpdated DATETIME DEFAULT NULL, TasksIdTasks INTEGER NOT NULL, JobsIdJobs INTEGER NOT NULL, ProvidersIdProviders INTEGER DEFAULT NULL, CONSTRAINT FK_417B578F2128991D FOREIGN KEY (TasksIdTasks) REFERENCES Tasks (Id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_417B578FCB864BD4 FOREIGN KEY (JobsIdJobs) REFERENCES Jobs (Id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_417B578F5A0B3B16 FOREIGN KEY (ProvidersIdProviders) REFERENCES Providers (Id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_417B578F2128991D ON TasksLoads (TasksIdTasks)');
        $this->addSql('CREATE INDEX IDX_417B578FCB864BD4 ON TasksLoads (JobsIdJobs)');
        $this->addSql('CREATE INDEX IDX_417B578F5A0B3B16 ON TasksLoads (ProvidersIdProviders)');
        $this->addSql("INSERT INTO Jobs (isTrashed, JobsLabel, JobsCreated) VALUES (0, 'General', CURRENT_TIMESTAMP)");
        $this->addSql("INSERT INTO States (isTrashed, StatesLabel, StatesCreated, StatesIsDefault) VALUES (0, 'Planned', CURRENT_TIMESTAMP, 1)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Jobs');
        $this->addSql('DROP TABLE Providers');
        $this->addSql('DROP TABLE States');
        $this->addSql('DROP TABLE Tasks');
        $this->addSql('DROP TABLE TasksLoads');
    }
}
