<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210610190154 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE session (
    			id VARCHAR(128) NOT NULL PRIMARY KEY,
    			sess_data BYTEA NOT NULL,
    			sess_lifetime INTEGER NOT NULL,
    			sess_time INTEGER NOT NULL
				);'
		);

		$this->addSql('CREATE INDEX session_sess_time_idx ON session (sess_lifetime);');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs

	}
}
