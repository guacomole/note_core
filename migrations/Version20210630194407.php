<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210630194407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX sessions_sess_time_idx');
        $this->addSql('ALTER TABLE sessions ALTER sess_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER INDEX uniq_8d93d649e7927c74 RENAME TO UNIQ_8D93D649F85E0677');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA core_app');
        $this->addSql('ALTER TABLE "user" DROP name');
        $this->addSql('ALTER INDEX uniq_8d93d649f85e0677 RENAME TO uniq_8d93d649e7927c74');
        $this->addSql('ALTER TABLE sessions ALTER sess_id TYPE VARCHAR(128)');
        $this->addSql('CREATE INDEX sessions_sess_time_idx ON sessions (sess_lifetime)');
    }
}
