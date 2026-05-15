<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * DDD refactor: add new columns to project table (manager_id, end_date,
 * development_status, development_notes, development_progress) and create
 * the project_technology many-to-many join table.
 *
 * These columns support the Core DDD Project aggregate which embeds the
 * Development concept and tracks Technology relationships directly.
 */
final class Version20260515000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add DDD Project aggregate columns: manager_id, end_date, development fields, and project_technology join table';
    }

    public function up(Schema $schema): void
    {
        // Add manager_id (nullable FK to app_user)
        $this->addSql('ALTER TABLE project ADD COLUMN manager_id UUID DEFAULT NULL');

        // Add end_date (nullable)
        $this->addSql('ALTER TABLE project ADD COLUMN end_date DATE DEFAULT NULL');

        // Add development embedded fields (nullable for backward compat with existing legacy rows)
        $this->addSql('ALTER TABLE project ADD COLUMN development_status VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD COLUMN development_notes TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD COLUMN development_progress INT DEFAULT NULL');

        // Create project_technology join table
        $this->addSql('CREATE TABLE project_technology (project_id UUID NOT NULL, technology_id UUID NOT NULL, PRIMARY KEY (project_id, technology_id))');
        $this->addSql('CREATE INDEX IDX_PROJECT_TECH_PROJECT ON project_technology (project_id)');
        $this->addSql('CREATE INDEX IDX_PROJECT_TECH_TECHNOLOGY ON project_technology (technology_id)');
        $this->addSql('ALTER TABLE project_technology ADD CONSTRAINT FK_PT_PROJECT FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE project_technology ADD CONSTRAINT FK_PT_TECHNOLOGY FOREIGN KEY (technology_id) REFERENCES technology (id) ON DELETE CASCADE NOT DEFERRABLE');

        // Also make description nullable for DDD aggregate (was NOT NULL in legacy)
        // NOTE: keep NOT NULL for backward compat; the DDD aggregate handles null at PHP level
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE project_technology');
        $this->addSql('ALTER TABLE project DROP COLUMN manager_id');
        $this->addSql('ALTER TABLE project DROP COLUMN end_date');
        $this->addSql('ALTER TABLE project DROP COLUMN development_status');
        $this->addSql('ALTER TABLE project DROP COLUMN development_notes');
        $this->addSql('ALTER TABLE project DROP COLUMN development_progress');
    }
}
