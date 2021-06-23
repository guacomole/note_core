<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210623200553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_sessions (user_id INT NOT NULL, session_id VARCHAR(255) NOT NULL, PRIMARY KEY(user_id, session_id))');
        $this->addSql('CREATE INDEX IDX_7AED7913A76ED395 ON user_sessions (user_id)');
        $this->addSql('CREATE INDEX IDX_7AED7913613FECDF ON user_sessions (session_id)');
        $this->addSql('ALTER TABLE user_sessions ADD CONSTRAINT FK_7AED7913A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_sessions ADD CONSTRAINT FK_7AED7913613FECDF FOREIGN KEY (session_id) REFERENCES sessions (sess_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_sessions');
    }
}
