<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260422070521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT fk_b4021e51903e0d4a');
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT fk_b4021e51cb9dce73');
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT fk_b4021e516c1197c9');
        $this->addSql('DROP INDEX uniq_b4021e51903e0d4a');
        $this->addSql('DROP INDEX uniq_b4021e51cb9dce73');
        $this->addSql('DROP INDEX uniq_b4021e516c1197c9');
        $this->addSql('ALTER TABLE project_user ADD project_id UUID NOT NULL');
        $this->addSql('ALTER TABLE project_user ADD app_user_id UUID NOT NULL');
        $this->addSql('ALTER TABLE project_user ADD project_role_id UUID NOT NULL');
        $this->addSql('ALTER TABLE project_user DROP project_id_id');
        $this->addSql('ALTER TABLE project_user DROP app_user_id_id');
        $this->addSql('ALTER TABLE project_user DROP project_role_id_id');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E514A3353D8 FOREIGN KEY (app_user_id) REFERENCES app_user (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51401D2EC9 FOREIGN KEY (project_role_id) REFERENCES project_role (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_B4021E51166D1F9C ON project_user (project_id)');
        $this->addSql('CREATE INDEX IDX_B4021E514A3353D8 ON project_user (app_user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B4021E51401D2EC9 ON project_user (project_role_id)');
        $this->addSql('ALTER TABLE time_entry DROP CONSTRAINT fk_6e537c0c85201e3d');
        $this->addSql('DROP INDEX idx_6e537c0c85201e3d');
        $this->addSql('ALTER TABLE time_entry RENAME COLUMN project_user_id_id TO project_user_id');
        $this->addSql('ALTER TABLE time_entry ADD CONSTRAINT FK_6E537C0C3170DFF0 FOREIGN KEY (project_user_id) REFERENCES project_user (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_6E537C0C3170DFF0 ON time_entry (project_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT FK_B4021E51166D1F9C');
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT FK_B4021E514A3353D8');
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT FK_B4021E51401D2EC9');
        $this->addSql('DROP INDEX IDX_B4021E51166D1F9C');
        $this->addSql('DROP INDEX IDX_B4021E514A3353D8');
        $this->addSql('DROP INDEX UNIQ_B4021E51401D2EC9');
        $this->addSql('ALTER TABLE project_user ADD project_id_id UUID NOT NULL');
        $this->addSql('ALTER TABLE project_user ADD app_user_id_id UUID NOT NULL');
        $this->addSql('ALTER TABLE project_user ADD project_role_id_id UUID NOT NULL');
        $this->addSql('ALTER TABLE project_user DROP project_id');
        $this->addSql('ALTER TABLE project_user DROP app_user_id');
        $this->addSql('ALTER TABLE project_user DROP project_role_id');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT fk_b4021e51903e0d4a FOREIGN KEY (project_role_id_id) REFERENCES project_role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT fk_b4021e51cb9dce73 FOREIGN KEY (app_user_id_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT fk_b4021e516c1197c9 FOREIGN KEY (project_id_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_b4021e51903e0d4a ON project_user (project_role_id_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_b4021e51cb9dce73 ON project_user (app_user_id_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_b4021e516c1197c9 ON project_user (project_id_id)');
        $this->addSql('ALTER TABLE time_entry DROP CONSTRAINT FK_6E537C0C3170DFF0');
        $this->addSql('DROP INDEX IDX_6E537C0C3170DFF0');
        $this->addSql('ALTER TABLE time_entry RENAME COLUMN project_user_id TO project_user_id_id');
        $this->addSql('ALTER TABLE time_entry ADD CONSTRAINT fk_6e537c0c85201e3d FOREIGN KEY (project_user_id_id) REFERENCES project_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6e537c0c85201e3d ON time_entry (project_user_id_id)');
    }
}
