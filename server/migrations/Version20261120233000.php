<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261120233000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add project milestones.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE KeyDates (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATE NOT NULL, details VARCHAR(255) NOT NULL, ProjectsIdProjects INTEGER NOT NULL, CONSTRAINT FK_48E12F9E890B443D FOREIGN KEY (ProjectsIdProjects) REFERENCES Projects (Id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_48E12F9E890B443D ON KeyDates (ProjectsIdProjects)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE KeyDates');
    }
}
