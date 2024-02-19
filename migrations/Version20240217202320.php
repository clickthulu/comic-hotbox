<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240217202320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comic ADD createdon DATETIME NOT NULL, ADD approved TINYINT(1) NOT NULL, ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE hot_box ADD createdon DATETIME NOT NULL');
        $this->addSql('ALTER TABLE image ADD approved TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP approved');
        $this->addSql('ALTER TABLE hot_box DROP createdon');
        $this->addSql('ALTER TABLE comic DROP createdon, DROP approved, DROP active');
    }
}
