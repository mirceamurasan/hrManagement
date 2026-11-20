<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120193655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Customers (isTrashed BOOLEAN DEFAULT 0 NOT NULL, Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CustomersName VARCHAR(50) DEFAULT NULL, CustomersCreatedBy INTEGER NOT NULL, CustomersCreated DATETIME NOT NULL, CustomersUpdated DATETIME DEFAULT NULL, AgenciesIdAgencies INTEGER DEFAULT NULL, mergedWith INTEGER DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E0A2CC821DCFFCBF ON Customers (CustomersName)');
        $this->addSql('CREATE TABLE Projects (isTrashed BOOLEAN DEFAULT 0 NOT NULL, Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ProjectsCreatedBy INTEGER NOT NULL, ProjectsName VARCHAR(250) DEFAULT NULL, ProjectsComments CLOB DEFAULT NULL, ProjectsCreated DATETIME NOT NULL, ProjectsUpdated DATETIME DEFAULT NULL, ProjectFirstTaskDate DATETIME DEFAULT NULL, ProjectLastTaskDate DATETIME DEFAULT NULL, ProjectsSuccess INTEGER NOT NULL, isOutsourcing BOOLEAN NOT NULL, SapNumber VARCHAR(50) DEFAULT NULL, InfoGeneral VARCHAR(50) DEFAULT NULL, InfoTech VARCHAR(50) DEFAULT NULL, CustomersIdCustomers INTEGER NOT NULL, ProjectStatus INTEGER DEFAULT NULL, mergedWith INTEGER DEFAULT NULL, CONSTRAINT FK_A5E5D1F21CA907DA FOREIGN KEY (CustomersIdCustomers) REFERENCES Customers (Id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A5E5D1F256BFCEDD FOREIGN KEY (ProjectStatus) REFERENCES ProjectStatuses (Id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A5E5D1F25C6E1A93 FOREIGN KEY (mergedWith) REFERENCES Projects (Id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A5E5D1F21CA907DA ON Projects (CustomersIdCustomers)');
        $this->addSql('CREATE INDEX IDX_A5E5D1F256BFCEDD ON Projects (ProjectStatus)');
        $this->addSql('CREATE INDEX IDX_A5E5D1F25C6E1A93 ON Projects (mergedWith)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Customers');
        $this->addSql('DROP TABLE Projects');
    }
}
