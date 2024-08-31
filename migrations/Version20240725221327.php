<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240725221327 extends AbstractMigration
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
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, setting VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, modifiedon DATETIME NOT NULL, defaultvalue VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, help LONGTEXT DEFAULT NULL, display_name VARCHAR(255) DEFAULT NULL, sourceoptions LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_E545A0C59F74B898 (setting), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('allow_user_signup', '0', '0', NOW(), 'bool', 'Allow users to register without approval.', 'Open user registration', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('server_name', null, null, NOW(), 'string', 'Name of this Server', 'Server Name', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('server_url', null, null, NOW(), 'string', 'Server\'s URL', 'Server URL', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('user_storage_path', 'storage', 'storage', NOW(), 'string', 'Path for user storage', 'User Storage Path', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('email_from_name', null, null, NOW(), 'string', 'Name used to send mail', 'From Name', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('email_from_address', null, null, NOW(), 'string', 'Email address used to send mail', 'From Address', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('image_limit', 6, 6, NOW(), 'int', 'Maximum number of images per comic', 'Image Limit', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('debug_mode', '0', '0', NOW(), 'bool', 'Turn on debug features for testing', 'Debug Mode:', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('brand_header',null,null, NOW(),'fileselect','Select a brand header from the media folder','Brand Header','../storage/_admin/*')");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('bug_tracking', null, null, NOW(), 'string', 'Location of a bug tracker/feature request site', 'Bug Tracking', null)");
    }

    /** @noinspection SqlResolve
     * @noinspection SqlNoDataSourceInspection
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE settings');
    }
}
