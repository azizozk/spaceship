<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260401005353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D866E3D4CF99EF0D ON robot_group (pudu_group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E422BFBAEFC17495 ON robot_in_group (sn)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_D866E3D4CF99EF0D');
        $this->addSql('DROP INDEX UNIQ_E422BFBAEFC17495');
    }
}
