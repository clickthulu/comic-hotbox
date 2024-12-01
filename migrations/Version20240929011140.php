<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240929011140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carousel (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, createdon DATETIME NOT NULL, active TINYINT(1) NOT NULL, width INT NOT NULL, height INT NOT NULL, display_type VARCHAR(255) NOT NULL, delay INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carousel_image (id INT AUTO_INCREMENT NOT NULL, comic_id INT NOT NULL, carousel_id INT NOT NULL, path VARCHAR(255) NOT NULL, createdon DATETIME NOT NULL, approved TINYINT(1) NOT NULL, width INT NOT NULL, height INT NOT NULL, ordinal INT NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_AABDD99D663094A (comic_id), INDEX IDX_AABDD99C1CE5B98 (carousel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comic (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, createdon DATETIME NOT NULL, approved TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, code VARCHAR(255) DEFAULT NULL, INDEX IDX_5B7EA5AAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hot_box (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, createdon DATETIME NOT NULL, active TINYINT(1) NOT NULL, rotation_frequency VARCHAR(255) NOT NULL, image_width INT NOT NULL, image_height INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, comic_id INT NOT NULL, path VARCHAR(255) NOT NULL, alttext LONGTEXT DEFAULT NULL, createdon DATETIME NOT NULL, approved TINYINT(1) NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, active TINYINT(1) NOT NULL, url VARCHAR(255) DEFAULT NULL, INDEX IDX_C53D045FD663094A (comic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration_code (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, createdon DATETIME NOT NULL, expireson DATETIME NOT NULL, activated TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rotation (id INT AUTO_INCREMENT NOT NULL, hotbox_id INT NOT NULL, comic_id INT NOT NULL, start DATETIME NOT NULL, expire DATETIME NOT NULL, ordinal INT NOT NULL, INDEX IDX_297C98F1798BABA1 (hotbox_id), INDEX IDX_297C98F1D663094A (comic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, setting VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, modifiedon DATETIME NOT NULL, defaultvalue VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, help LONGTEXT DEFAULT NULL, display_name VARCHAR(255) DEFAULT NULL, sourceoptions LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_E545A0C59F74B898 (setting), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, createdon DATETIME NOT NULL, active TINYINT(1) NOT NULL, deleted TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE webring (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, createdon DATETIME NOT NULL, active TINYINT(1) NOT NULL, number_images INT NOT NULL, navigation_width INT NOT NULL, ring_width INT NOT NULL, ring_height INT NOT NULL, navigation_left VARCHAR(255) DEFAULT NULL, navigation_right VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE webring_image (id INT AUTO_INCREMENT NOT NULL, comic_id INT NOT NULL, webring_id INT NOT NULL, path VARCHAR(255) NOT NULL, createdon DATETIME NOT NULL, ordinal INT NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_771E367D663094A (comic_id), INDEX IDX_771E3673EDA109E (webring_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carousel_image ADD CONSTRAINT FK_AABDD99D663094A FOREIGN KEY (comic_id) REFERENCES comic (id)');
        $this->addSql('ALTER TABLE carousel_image ADD CONSTRAINT FK_AABDD99C1CE5B98 FOREIGN KEY (carousel_id) REFERENCES carousel (id)');
        $this->addSql('ALTER TABLE comic ADD CONSTRAINT FK_5B7EA5AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FD663094A FOREIGN KEY (comic_id) REFERENCES comic (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rotation ADD CONSTRAINT FK_297C98F1798BABA1 FOREIGN KEY (hotbox_id) REFERENCES hot_box (id)');
        $this->addSql('ALTER TABLE rotation ADD CONSTRAINT FK_297C98F1D663094A FOREIGN KEY (comic_id) REFERENCES comic (id)');
        $this->addSql('ALTER TABLE webring_image ADD CONSTRAINT FK_771E367D663094A FOREIGN KEY (comic_id) REFERENCES comic (id)');
        $this->addSql('ALTER TABLE webring_image ADD CONSTRAINT FK_771E3673EDA109E FOREIGN KEY (webring_id) REFERENCES webring (id)');


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
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('enable_hotbox', '1', '1', NOW(), 'bool', 'Enable Hotboxes', 'Enable Hotboxes:', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('enable_webring', '0', '0', NOW(), 'bool', 'Enable Webrings', 'Enable Webrings:', null)");
        $this->addSql("INSERT INTO settings (setting, value, defaultvalue, modifiedon, type, help, display_name, sourceoptions) values ('enable_carousel', '0', '0', NOW(), 'bool', 'Enable Carousels', 'Enable Carousels:', null)");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carousel_image DROP FOREIGN KEY FK_AABDD99D663094A');
        $this->addSql('ALTER TABLE carousel_image DROP FOREIGN KEY FK_AABDD99C1CE5B98');
        $this->addSql('ALTER TABLE comic DROP FOREIGN KEY FK_5B7EA5AAA76ED395');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FD663094A');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE rotation DROP FOREIGN KEY FK_297C98F1798BABA1');
        $this->addSql('ALTER TABLE rotation DROP FOREIGN KEY FK_297C98F1D663094A');
        $this->addSql('ALTER TABLE webring_image DROP FOREIGN KEY FK_771E367D663094A');
        $this->addSql('ALTER TABLE webring_image DROP FOREIGN KEY FK_771E3673EDA109E');
        $this->addSql('DROP TABLE carousel');
        $this->addSql('DROP TABLE carousel_image');
        $this->addSql('DROP TABLE comic');
        $this->addSql('DROP TABLE hot_box');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE registration_code');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE rotation');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE webring');
        $this->addSql('DROP TABLE webring_image');
    }
}
