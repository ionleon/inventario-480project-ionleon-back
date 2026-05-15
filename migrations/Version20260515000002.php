<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * DDD refactor: add new columns to the link table for the Core Link aggregate.
 *
 * The Core Link aggregate references ProjectId directly instead of DevelopmentId,
 * and adds label and created_at fields. Existing legacy columns (enviroment,
 * development_id) are preserved for backward compat with the legacy layer.
 */
final class Version20260515000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add DDD Link aggregate columns: project_id, label, created_at';
    }

    public function up(Schema $schema): void
    {
        // Add project_id (nullable initially for backward compat with existing legacy rows)
        $this->addSql('ALTER TABLE link ADD COLUMN project_id UUID DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_LINK_PROJECT ON link (project_id)');

        // Add label (optional human-readable label for the link)
        $this->addSql('ALTER TABLE link ADD COLUMN label VARCHAR(100) DEFAULT NULL');

        // Add created_at (defaults to now for existing rows)
        $this->addSql("ALTER TABLE link ADD COLUMN created_at TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT NOW()");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_LINK_PROJECT');
        $this->addSql('ALTER TABLE link DROP COLUMN project_id');
        $this->addSql('ALTER TABLE link DROP COLUMN label');
        $this->addSql('ALTER TABLE link DROP COLUMN created_at');
    }
}
