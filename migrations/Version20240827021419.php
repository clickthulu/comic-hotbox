<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240827021419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    /** @noinspection SqlResolve
     * @noinspection SqlNoDataSourceInspection
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('enable_hotbox', '1', '1', NOW(), 'bool', 'Enable Hotboxes', 'Enable Hotboxes:', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('enable_webring', '0', '0', NOW(), 'bool', 'Enable Webrings', 'Enable Webrings:', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('enable_carousel', '0', '0', NOW(), 'bool', 'Enable Carousels', 'Enable Carousels:', null)");


    }

    public function down(Schema $schema): void
    {
    }
}
