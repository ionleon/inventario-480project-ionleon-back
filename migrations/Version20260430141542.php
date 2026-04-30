<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260430141542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_user (id UUID NOT NULL, name VARCHAR(100) NOT NULL, surname VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, password VARCHAR(255) NOT NULL, first_time BOOLEAN NOT NULL, is_active BOOLEAN NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9E7927C74 ON app_user (email)');
        $this->addSql('CREATE TABLE client (id UUID NOT NULL, name VARCHAR(120) NOT NULL, is_active BOOLEAN NOT NULL, sector_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C7440455DE95C867 ON client (sector_id)');
        $this->addSql('CREATE TABLE contact (id UUID NOT NULL, full_name VARCHAR(255) NOT NULL, phone_number VARCHAR(30) NOT NULL, email VARCHAR(255) NOT NULL, is_main BOOLEAN NOT NULL, note TEXT DEFAULT NULL, client_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_4C62E63819EB6921 ON contact (client_id)');
        $this->addSql('CREATE TABLE development (id UUID NOT NULL, name VARCHAR(100) NOT NULL, description TEXT NOT NULL, url_repository TEXT NOT NULL, project_id UUID NOT NULL, technology_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C0D6212A5E237E06 ON development (name)');
        $this->addSql('CREATE INDEX IDX_C0D6212A166D1F9C ON development (project_id)');
        $this->addSql('CREATE INDEX IDX_C0D6212A4235D463 ON development (technology_id)');
        $this->addSql('CREATE TABLE link (id UUID NOT NULL, enviroment VARCHAR(255) NOT NULL, url TEXT NOT NULL, development_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_36AC99F1B0B464C4 ON link (development_id)');
        $this->addSql('CREATE TABLE project (id UUID NOT NULL, name VARCHAR(150) NOT NULL, description TEXT NOT NULL, start_date DATE DEFAULT NULL, is_active BOOLEAN NOT NULL, client_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE19EB6921 ON project (client_id)');
        $this->addSql('CREATE TABLE project_role (id UUID NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE project_user (id UUID NOT NULL, project_id UUID NOT NULL, app_user_id UUID NOT NULL, project_role_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_B4021E51166D1F9C ON project_user (project_id)');
        $this->addSql('CREATE INDEX IDX_B4021E514A3353D8 ON project_user (app_user_id)');
        $this->addSql('CREATE INDEX IDX_B4021E51401D2EC9 ON project_user (project_role_id)');
        $this->addSql('CREATE TABLE sector (id UUID NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4BA3D9E85E237E06 ON sector (name)');
        $this->addSql('CREATE TABLE technology (id UUID NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F463524D5E237E06 ON technology (name)');
        $this->addSql('CREATE TABLE time_entry (id UUID NOT NULL, date DATE NOT NULL, hour NUMERIC(7, 2) NOT NULL, comment VARCHAR(150) DEFAULT NULL, project_user_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_6E537C0C3170DFF0 ON time_entry (project_user_id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63819EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE development ADD CONSTRAINT FK_C0D6212A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE development ADD CONSTRAINT FK_C0D6212A4235D463 FOREIGN KEY (technology_id) REFERENCES technology (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F1B0B464C4 FOREIGN KEY (development_id) REFERENCES development (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E514A3353D8 FOREIGN KEY (app_user_id) REFERENCES app_user (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51401D2EC9 FOREIGN KEY (project_role_id) REFERENCES project_role (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE time_entry ADD CONSTRAINT FK_6E537C0C3170DFF0 FOREIGN KEY (project_user_id) REFERENCES project_user (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C7440455DE95C867');
        $this->addSql('ALTER TABLE contact DROP CONSTRAINT FK_4C62E63819EB6921');
        $this->addSql('ALTER TABLE development DROP CONSTRAINT FK_C0D6212A166D1F9C');
        $this->addSql('ALTER TABLE development DROP CONSTRAINT FK_C0D6212A4235D463');
        $this->addSql('ALTER TABLE link DROP CONSTRAINT FK_36AC99F1B0B464C4');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE19EB6921');
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT FK_B4021E51166D1F9C');
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT FK_B4021E514A3353D8');
        $this->addSql('ALTER TABLE project_user DROP CONSTRAINT FK_B4021E51401D2EC9');
        $this->addSql('ALTER TABLE time_entry DROP CONSTRAINT FK_6E537C0C3170DFF0');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE development');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_role');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('DROP TABLE sector');
        $this->addSql('DROP TABLE technology');
        $this->addSql('DROP TABLE time_entry');
    }
}
