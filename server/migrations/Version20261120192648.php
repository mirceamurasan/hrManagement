<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120192648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE DaysoffsGlobal (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, DaysoffsGlobalDate DATE NOT NULL, DaysoffsGlobalName VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE TABLE ProjectStatuses (isTrashed BOOLEAN DEFAULT 0 NOT NULL, Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ProjectStatusesLabel VARCHAR(45) NOT NULL, ProjectStatusesCreated DATETIME NOT NULL, ProjectStatusesUpdated DATETIME DEFAULT NULL, ProjectStatusesIsFinal BOOLEAN DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1E12662E84E0BA6B ON DaysoffsGlobal (DaysoffsGlobalDate)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1E12662E705DF317 ON DaysoffsGlobal (DaysoffsGlobalName)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D815AFC15D179958 ON ProjectStatuses (ProjectStatusesLabel)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE DaysoffsGlobal');
        $this->addSql('DROP TABLE ProjectStatuses');
    }
}
