<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260421093852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE development DROP CONSTRAINT fk_c0d6212a6c1197c9');
        $this->addSql('ALTER TABLE development DROP CONSTRAINT fk_c0d6212a9f377433');
        $this->addSql('DROP INDEX idx_c0d6212a6c1197c9');
        $this->addSql('DROP INDEX idx_c0d6212a9f377433');
        $this->addSql('ALTER TABLE development ADD project_id UUID NOT NULL');
        $this->addSql('ALTER TABLE development ADD technology_id UUID NOT NULL');
        $this->addSql('ALTER TABLE development DROP project_id_id');
        $this->addSql('ALTER TABLE development DROP technology_id_id');
        $this->addSql('ALTER TABLE development ADD CONSTRAINT FK_C0D6212A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE development ADD CONSTRAINT FK_C0D6212A4235D463 FOREIGN KEY (technology_id) REFERENCES technology (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_C0D6212A166D1F9C ON development (project_id)');
        $this->addSql('CREATE INDEX IDX_C0D6212A4235D463 ON development (technology_id)');
        $this->addSql('ALTER TABLE link DROP CONSTRAINT fk_36ac99f13d7a222a');
        $this->addSql('DROP INDEX idx_36ac99f13d7a222a');
        $this->addSql('ALTER TABLE link RENAME COLUMN development_id_id TO development_id');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT FK_36AC99F1B0B464C4 FOREIGN KEY (development_id) REFERENCES development (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_36AC99F1B0B464C4 ON link (development_id)');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT fk_2fb3d0eedc2902e0');
        $this->addSql('DROP INDEX idx_2fb3d0eedc2902e0');
        $this->addSql('ALTER TABLE project ADD is_active BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE project RENAME COLUMN client_id_id TO client_id');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE19EB6921 ON project (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE development DROP CONSTRAINT FK_C0D6212A166D1F9C');
        $this->addSql('ALTER TABLE development DROP CONSTRAINT FK_C0D6212A4235D463');
        $this->addSql('DROP INDEX IDX_C0D6212A166D1F9C');
        $this->addSql('DROP INDEX IDX_C0D6212A4235D463');
        $this->addSql('ALTER TABLE development ADD project_id_id UUID NOT NULL');
        $this->addSql('ALTER TABLE development ADD technology_id_id UUID NOT NULL');
        $this->addSql('ALTER TABLE development DROP project_id');
        $this->addSql('ALTER TABLE development DROP technology_id');
        $this->addSql('ALTER TABLE development ADD CONSTRAINT fk_c0d6212a6c1197c9 FOREIGN KEY (project_id_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE development ADD CONSTRAINT fk_c0d6212a9f377433 FOREIGN KEY (technology_id_id) REFERENCES technology (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c0d6212a6c1197c9 ON development (project_id_id)');
        $this->addSql('CREATE INDEX idx_c0d6212a9f377433 ON development (technology_id_id)');
        $this->addSql('ALTER TABLE link DROP CONSTRAINT FK_36AC99F1B0B464C4');
        $this->addSql('DROP INDEX IDX_36AC99F1B0B464C4');
        $this->addSql('ALTER TABLE link RENAME COLUMN development_id TO development_id_id');
        $this->addSql('ALTER TABLE link ADD CONSTRAINT fk_36ac99f13d7a222a FOREIGN KEY (development_id_id) REFERENCES development (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_36ac99f13d7a222a ON link (development_id_id)');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE19EB6921');
        $this->addSql('DROP INDEX IDX_2FB3D0EE19EB6921');
        $this->addSql('ALTER TABLE project DROP is_active');
        $this->addSql('ALTER TABLE project RENAME COLUMN client_id TO client_id_id');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT fk_2fb3d0eedc2902e0 FOREIGN KEY (client_id_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2fb3d0eedc2902e0 ON project (client_id_id)');
    }
}
