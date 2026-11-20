<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261120210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add worker profiles and connect task allocations to workers.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE Workers (isTrashed BOOLEAN DEFAULT 0 NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, WorkersName VARCHAR(150) NOT NULL, WorkersAccount INTEGER DEFAULT NULL, WorkersJob INTEGER DEFAULT NULL, StartWorkingDate DATE DEFAULT NULL, EndWorkingDate DATE DEFAULT NULL, WorkersCreated DATETIME NOT NULL, CONSTRAINT FK_7790445CE5D91BB4 FOREIGN KEY (WorkersAccount) REFERENCES app_users (id) ON UPDATE NO ACTION ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7790445CC22968FE FOREIGN KEY (WorkersJob) REFERENCES Jobs (Id) ON UPDATE NO ACTION ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7790445CE5D91BB4 ON Workers (WorkersAccount)');
        $this->addSql('CREATE INDEX IDX_7790445CC22968FE ON Workers (WorkersJob)');
        $this->addSql("INSERT INTO Workers (id, isTrashed, WorkersName, WorkersCreated) SELECT DISTINCT TasksLoadsWorkers, 0, 'Imported worker #' || TasksLoadsWorkers, CURRENT_TIMESTAMP FROM TasksLoads WHERE TasksLoadsWorkers IS NOT NULL");
        $this->addSql('CREATE TABLE TasksLoads_new (isTrashed BOOLEAN DEFAULT 0 NOT NULL, Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, TasksLoadsWorkers INTEGER DEFAULT NULL, TasksLoadsVolume SMALLINT NOT NULL, TasksLoadsCreatedBy INTEGER NOT NULL, TasksLoadsCreated DATETIME NOT NULL, TasksLoadsUpdated DATETIME DEFAULT NULL, TasksIdTasks INTEGER NOT NULL, JobsIdJobs INTEGER NOT NULL, ProvidersIdProviders INTEGER DEFAULT NULL, CONSTRAINT FK_417B578F2128991D FOREIGN KEY (TasksIdTasks) REFERENCES Tasks (Id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_417B578FCB864BD4 FOREIGN KEY (JobsIdJobs) REFERENCES Jobs (Id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_417B578F5A0B3B16 FOREIGN KEY (ProvidersIdProviders) REFERENCES Providers (Id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_417B578FB5EF088E FOREIGN KEY (TasksLoadsWorkers) REFERENCES Workers (id) ON UPDATE NO ACTION ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO TasksLoads_new (isTrashed, Id, TasksLoadsWorkers, TasksLoadsVolume, TasksLoadsCreatedBy, TasksLoadsCreated, TasksLoadsUpdated, TasksIdTasks, JobsIdJobs, ProvidersIdProviders) SELECT isTrashed, Id, TasksLoadsWorkers, TasksLoadsVolume, TasksLoadsCreatedBy, TasksLoadsCreated, TasksLoadsUpdated, TasksIdTasks, JobsIdJobs, ProvidersIdProviders FROM TasksLoads');
        $this->addSql('DROP TABLE TasksLoads');
        $this->addSql('ALTER TABLE TasksLoads_new RENAME TO TasksLoads');
        $this->addSql('CREATE INDEX IDX_417B578F2128991D ON TasksLoads (TasksIdTasks)');
        $this->addSql('CREATE INDEX IDX_417B578FCB864BD4 ON TasksLoads (JobsIdJobs)');
        $this->addSql('CREATE INDEX IDX_417B578F5A0B3B16 ON TasksLoads (ProvidersIdProviders)');
        $this->addSql('CREATE INDEX IDX_417B578FB5EF088E ON TasksLoads (TasksLoadsWorkers)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE TasksLoads_old (isTrashed BOOLEAN DEFAULT 0 NOT NULL, Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, TasksLoadsWorkers INTEGER DEFAULT NULL, TasksLoadsVolume SMALLINT NOT NULL, TasksLoadsCreatedBy INTEGER NOT NULL, TasksLoadsCreated DATETIME NOT NULL, TasksLoadsUpdated DATETIME DEFAULT NULL, TasksIdTasks INTEGER NOT NULL, JobsIdJobs INTEGER NOT NULL, ProvidersIdProviders INTEGER DEFAULT NULL, CONSTRAINT FK_417B578F2128991D FOREIGN KEY (TasksIdTasks) REFERENCES Tasks (Id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_417B578FCB864BD4 FOREIGN KEY (JobsIdJobs) REFERENCES Jobs (Id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_417B578F5A0B3B16 FOREIGN KEY (ProvidersIdProviders) REFERENCES Providers (Id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO TasksLoads_old (isTrashed, Id, TasksLoadsWorkers, TasksLoadsVolume, TasksLoadsCreatedBy, TasksLoadsCreated, TasksLoadsUpdated, TasksIdTasks, JobsIdJobs, ProvidersIdProviders) SELECT isTrashed, Id, TasksLoadsWorkers, TasksLoadsVolume, TasksLoadsCreatedBy, TasksLoadsCreated, TasksLoadsUpdated, TasksIdTasks, JobsIdJobs, ProvidersIdProviders FROM TasksLoads');
        $this->addSql('DROP TABLE TasksLoads');
        $this->addSql('ALTER TABLE TasksLoads_old RENAME TO TasksLoads');
        $this->addSql('CREATE INDEX IDX_417B578F2128991D ON TasksLoads (TasksIdTasks)');
        $this->addSql('CREATE INDEX IDX_417B578FCB864BD4 ON TasksLoads (JobsIdJobs)');
        $this->addSql('CREATE INDEX IDX_417B578F5A0B3B16 ON TasksLoads (ProvidersIdProviders)');
        $this->addSql('DROP TABLE Workers');
    }
}
