<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240904013303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE webring_image (id INT AUTO_INCREMENT NOT NULL, comic_id INT NOT NULL, webring_id INT NOT NULL, path VARCHAR(255) NOT NULL, createdon DATETIME NOT NULL, ordinal INT NOT NULL, INDEX IDX_771E367D663094A (comic_id), INDEX IDX_771E3673EDA109E (webring_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE webring_image ADD CONSTRAINT FK_771E367D663094A FOREIGN KEY (comic_id) REFERENCES comic (id)');
        $this->addSql('ALTER TABLE webring_image ADD CONSTRAINT FK_771E3673EDA109E FOREIGN KEY (webring_id) REFERENCES webring (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE webring_image DROP FOREIGN KEY FK_771E367D663094A');
        $this->addSql('ALTER TABLE webring_image DROP FOREIGN KEY FK_771E3673EDA109E');
        $this->addSql('DROP TABLE webring_image');
    }
}
