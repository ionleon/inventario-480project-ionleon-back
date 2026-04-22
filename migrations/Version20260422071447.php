<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260422071447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_b4021e51401d2ec9');
        $this->addSql('CREATE INDEX IDX_B4021E51401D2EC9 ON project_user (project_role_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_B4021E51401D2EC9');
        $this->addSql('CREATE UNIQUE INDEX uniq_b4021e51401d2ec9 ON project_user (project_role_id)');
    }
}
