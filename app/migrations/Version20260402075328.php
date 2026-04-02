<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260402075328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create lock_keys table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE lock_keys (
              key_id VARCHAR(64) NOT NULL,
              key_token VARCHAR(44) NOT NULL,
              key_expiration INT NOT NULL,
              PRIMARY KEY (key_id)
            )
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE lock_keys');
    }
}
