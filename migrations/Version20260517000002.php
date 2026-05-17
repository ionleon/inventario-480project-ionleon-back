<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Final schema alignment: drops remaining legacy FK/index constraints and
 * adjusts column types/lengths to match the Core aggregate mappings.
 *
 * Picks up where Version20260517000001 left off (its scope was wider than
 * a single migration cleanly handles).
 */
final class Version20260517000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop remaining legacy FKs/indexes, adjust column types to Core aggregate mappings';
    }

    public function up(Schema $schema): void
    {
        // Drop remaining legacy FK constraints on project_user, time_entry
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT IF EXISTS fk_b4021e51166d1f9c');
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT IF EXISTS fk_b4021e514a3353d8');
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT IF EXISTS fk_b4021e51401d2ec9');
        $this->addSql('DROP INDEX IF EXISTS idx_b4021e51401d2ec9');
        $this->addSql('DROP INDEX IF EXISTS idx_b4021e51166d1f9c');
        $this->addSql('DROP INDEX IF EXISTS idx_b4021e514a3353d8');
        $this->addSql('ALTER TABLE project_user ALTER COLUMN allocation DROP DEFAULT');

        $this->addSql('ALTER TABLE time_entry DROP CONSTRAINT IF EXISTS fk_6e537c0c3170dff0');
        $this->addSql('DROP INDEX IF EXISTS idx_6e537c0c3170dff0');
        $this->addSql('ALTER TABLE time_entry ALTER COLUMN comment TYPE VARCHAR(500)');

        // Tighten varchar lengths to match aggregate VOs
        $this->addSql('ALTER TABLE technology ALTER COLUMN name TYPE VARCHAR(80)');
        $this->addSql('ALTER TABLE project_role ALTER COLUMN name TYPE VARCHAR(80)');

        // Drop legacy unique indexes
        $this->addSql('DROP INDEX IF EXISTS uniq_f463524d5e237e06');
        $this->addSql('DROP INDEX IF EXISTS uniq_4ba3d9e85e237e06');

        // Rename project_technology indexes to Doctrine convention
        $this->addSql('ALTER INDEX IF EXISTS idx_project_tech_project RENAME TO IDX_ECC5297F166D1F9C');
        $this->addSql('ALTER INDEX IF EXISTS idx_project_tech_technology RENAME TO IDX_ECC5297F4235D463');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER INDEX IF EXISTS IDX_ECC5297F4235D463 RENAME TO idx_project_tech_technology');
        $this->addSql('ALTER INDEX IF EXISTS IDX_ECC5297F166D1F9C RENAME TO idx_project_tech_project');
        $this->addSql('ALTER TABLE time_entry ALTER COLUMN comment TYPE VARCHAR(150)');
    }
}
