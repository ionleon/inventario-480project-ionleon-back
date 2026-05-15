<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * DDD refactor: add allocation column to project_user for the Core ProjectUser aggregate.
 *
 * The Core ProjectUser aggregate introduces an allocation percentage (0-100) that
 * did not exist in the legacy schema. Added as nullable SMALLINT with default 100
 * to preserve backward compatibility with existing rows.
 */
final class Version20260515000003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add DDD ProjectUser aggregate column: allocation';
    }

    public function up(Schema $schema): void
    {
        // allocation: 0-100 percentage, nullable for existing rows, defaults to 100
        $this->addSql('ALTER TABLE project_user ADD COLUMN allocation SMALLINT DEFAULT 100');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project_user DROP COLUMN allocation');
    }
}
