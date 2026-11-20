<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261120235000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add directory email and initials to worker profiles.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE Workers ADD COLUMN WorkersEmail VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE Workers ADD COLUMN WorkersInitials VARCHAR(5) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7790445C52ABE911 ON Workers (WorkersEmail)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_7790445C52ABE911');
        $this->addSql('ALTER TABLE Workers DROP COLUMN WorkersEmail');
        $this->addSql('ALTER TABLE Workers DROP COLUMN WorkersInitials');
    }
}
