<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261120220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add contract types, employers and worker contracts.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ContractTypes (isTrashed BOOLEAN DEFAULT 0 NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ContractTypesName VARCHAR(50) NOT NULL, ContractTypesCreated DATETIME NOT NULL, ContractTypesUpdated DATETIME DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1EDFD3B23A0A38F3 ON ContractTypes (ContractTypesName)');
        $this->addSql('CREATE TABLE Employers (isTrashed BOOLEAN DEFAULT 0 NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, EmployersName VARCHAR(50) NOT NULL, EmployersCreated DATETIME NOT NULL, EmployersUpdated DATETIME DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3DF0C535E25420AC ON Employers (EmployersName)');
        $this->addSql('CREATE TABLE Contracts (isTrashed BOOLEAN DEFAULT 0 NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, comment CLOB DEFAULT NULL, ContractsCreated DATETIME NOT NULL, ContractsUpdated DATETIME DEFAULT NULL, idworker INTEGER NOT NULL, contract_type_id INTEGER NOT NULL, employer_id INTEGER NOT NULL, idjob INTEGER NOT NULL, CONSTRAINT FK_8BA12BD0FB0F12CE FOREIGN KEY (idworker) REFERENCES Workers (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8BA12BD0CD1DF15B FOREIGN KEY (contract_type_id) REFERENCES ContractTypes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8BA12BD041CD9E7A FOREIGN KEY (employer_id) REFERENCES Employers (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8BA12BD0E2DDBB9F FOREIGN KEY (idjob) REFERENCES Jobs (Id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8BA12BD0FB0F12CE ON Contracts (idworker)');
        $this->addSql('CREATE INDEX IDX_8BA12BD0CD1DF15B ON Contracts (contract_type_id)');
        $this->addSql('CREATE INDEX IDX_8BA12BD041CD9E7A ON Contracts (employer_id)');
        $this->addSql('CREATE INDEX IDX_8BA12BD0E2DDBB9F ON Contracts (idjob)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE Contracts');
        $this->addSql('DROP TABLE ContractTypes');
        $this->addSql('DROP TABLE Employers');
    }
}
