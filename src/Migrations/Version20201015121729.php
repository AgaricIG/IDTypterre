<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201015121729 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE uts_ucs (uts_id INT NOT NULL, ucs_id INT NOT NULL, PRIMARY KEY(uts_id, ucs_id))');
        $this->addSql('CREATE INDEX IDX_4B7558976F5688E ON uts_ucs (uts_id)');
        $this->addSql('CREATE INDEX IDX_4B755897D435231C ON uts_ucs (ucs_id)');
        $this->addSql('ALTER TABLE uts_ucs ADD CONSTRAINT FK_4B7558976F5688E FOREIGN KEY (uts_id) REFERENCES uts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE uts_ucs ADD CONSTRAINT FK_4B755897D435231C FOREIGN KEY (ucs_id) REFERENCES ucs (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE uts_ucs');
    }
}
