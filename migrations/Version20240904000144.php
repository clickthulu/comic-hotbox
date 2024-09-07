<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240904000144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE webring (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, createdon DATETIME NOT NULL, active TINYINT(1) NOT NULL, ordinal INT NOT NULL, number_images INT NOT NULL, image_width INT NOT NULL, image_height INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carousel_image CHANGE active active TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE webring');
        $this->addSql('ALTER TABLE carousel_image CHANGE active active TINYINT(1) DEFAULT 1 NOT NULL');
    }
}
