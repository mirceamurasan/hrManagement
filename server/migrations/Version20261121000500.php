<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261121000500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add worker assignments to customers.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE CustomerWorkerAssignments (isTrashed BOOLEAN DEFAULT 0 NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, customer_id INTEGER NOT NULL, worker_id INTEGER NOT NULL, CONSTRAINT FK_768C358F9395C3F3 FOREIGN KEY (customer_id) REFERENCES Customers (Id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_768C358F6B20BA36 FOREIGN KEY (worker_id) REFERENCES Workers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CUSTOMER_WORKER_ACTIVE ON CustomerWorkerAssignments (customer_id, worker_id)');
        $this->addSql('CREATE INDEX IDX_768C358F9395C3F3 ON CustomerWorkerAssignments (customer_id)');
        $this->addSql('CREATE INDEX IDX_768C358F6B20BA36 ON CustomerWorkerAssignments (worker_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE CustomerWorkerAssignments');
    }
}
