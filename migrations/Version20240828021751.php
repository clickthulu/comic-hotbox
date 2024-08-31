<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240828021751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carousel (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, createdon DATETIME NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carousel_image (id INT AUTO_INCREMENT NOT NULL, comic_id INT NOT NULL, carousel_id INT NOT NULL, path VARCHAR(255) NOT NULL, createdon DATETIME NOT NULL, approved TINYINT(1) NOT NULL, width INT NOT NULL, height INT NOT NULL, INDEX IDX_AABDD99D663094A (comic_id), INDEX IDX_AABDD99C1CE5B98 (carousel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carousel_image ADD CONSTRAINT FK_AABDD99D663094A FOREIGN KEY (comic_id) REFERENCES comic (id)');
        $this->addSql('ALTER TABLE carousel_image ADD CONSTRAINT FK_AABDD99C1CE5B98 FOREIGN KEY (carousel_id) REFERENCES carousel (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carousel_image DROP FOREIGN KEY FK_AABDD99D663094A');
        $this->addSql('ALTER TABLE carousel_image DROP FOREIGN KEY FK_AABDD99C1CE5B98');
        $this->addSql('DROP TABLE carousel');
        $this->addSql('DROP TABLE carousel_image');
    }
}
