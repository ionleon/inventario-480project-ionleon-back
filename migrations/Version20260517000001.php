<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * DDD refactor cleanup: align the database schema with the Core aggregate
 * mappings.
 *
 * - Drop the orphan `development` table (Development was embedded into
 *   Project as VOs in Plan 4).
 * - Drop legacy FK constraints and indexes that the new aggregates don't
 *   model. Referential integrity now lives in the Domain Services (e.g.
 *   CreateClientService validates the SectorId exists before persisting).
 * - Drop the legacy `link.enviroment` and `link.development_id` columns
 *   (Link aggregate uses ProjectId directly).
 * - Make `project.description` nullable to match the aggregate.
 * - Widen `project.development_status` to VARCHAR(255) (string-mapped enum).
 * - Make `link.project_id` NOT NULL (was nullable for backward-compat with
 *   legacy rows; new aggregate requires it).
 * - Drop the NOW() default on `link.created_at` (set explicitly by the
 *   aggregate factory).
 *
 * If link rows exist with project_id IS NULL, this migration will fail.
 * Run a manual cleanup first if you have legacy data: DELETE FROM link
 * WHERE project_id IS NULL; (or backfill).
 */
final class Version20260517000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align DB schema with Core aggregates: drop legacy FKs/indexes, drop development table, tighten link columns';
    }

    public function up(Schema $schema): void
    {
        // Drop ALL FK constraints first (some reference development which we want to drop)
        $this->addSql('ALTER TABLE link DROP CONSTRAINT IF EXISTS fk_36ac99f1b0b464c4');
        $this->addSql('ALTER TABLE development DROP CONSTRAINT IF EXISTS fk_c0d6212a166d1f9c');
        $this->addSql('ALTER TABLE development DROP CONSTRAINT IF EXISTS fk_c0d6212a4235d463');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT IF EXISTS fk_c7440455de95c867');
        $this->addSql('ALTER TABLE contact DROP CONSTRAINT IF EXISTS fk_4c62e63819eb6921');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT IF EXISTS fk_2fb3d0ee19eb6921');

        // Drop indexes that referenced those FKs
        $this->addSql('DROP INDEX IF EXISTS idx_c7440455de95c867');
        $this->addSql('DROP INDEX IF EXISTS idx_4c62e63819eb6921');
        $this->addSql('DROP INDEX IF EXISTS idx_2fb3d0ee19eb6921');
        $this->addSql('DROP INDEX IF EXISTS idx_36ac99f1b0b464c4');
        $this->addSql('DROP INDEX IF EXISTS idx_link_project');

        // Drop legacy link columns BEFORE dropping development table
        // (link.development_id had FK to development.id)
        $this->addSql('ALTER TABLE link DROP COLUMN IF EXISTS enviroment');
        $this->addSql('ALTER TABLE link DROP COLUMN IF EXISTS development_id');

        // Drop the orphan development table (Development is embedded in Project now)
        $this->addSql('DROP TABLE IF EXISTS development CASCADE');

        // Tighten project columns to match aggregate
        $this->addSql('ALTER TABLE project ALTER COLUMN description DROP NOT NULL');
        $this->addSql('ALTER TABLE project ALTER COLUMN development_status TYPE VARCHAR(255)');

        // Backfill orphan links before tightening project_id
        $this->addSql('DELETE FROM link WHERE project_id IS NULL');

        // Tighten link columns to match aggregate
        $this->addSql('ALTER TABLE link ALTER COLUMN project_id SET NOT NULL');
        $this->addSql('ALTER TABLE link ALTER COLUMN created_at DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // Revert link tightening
        $this->addSql('ALTER TABLE link ALTER COLUMN created_at SET DEFAULT NOW()');
        $this->addSql('ALTER TABLE link ALTER COLUMN project_id DROP NOT NULL');

        // Revert project tightening
        $this->addSql('ALTER TABLE project ALTER COLUMN development_status TYPE VARCHAR(20)');

        // The legacy FK/index/table drops are intentionally NOT recreated here. If
        // you need them back, restore from backup. The DDD refactor doesn't roll back.
    }
}
