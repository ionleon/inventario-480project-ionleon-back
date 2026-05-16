<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * DDD refactor cleanup: make legacy nullable columns no longer enforced for
 * the new Core aggregates which don't model these concepts (enviroment on
 * link, etc.).
 */
final class Version20260516000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Relax NOT NULL on legacy columns no longer modeled by Core aggregates';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE link ALTER COLUMN enviroment DROP NOT NULL');
        $this->addSql('ALTER TABLE link ALTER COLUMN development_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE link ALTER COLUMN enviroment SET NOT NULL');
        $this->addSql('ALTER TABLE link ALTER COLUMN development_id SET NOT NULL');
    }
}
