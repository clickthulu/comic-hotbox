<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240217193211 extends AbstractMigration
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
        $this->addSql('CREATE TABLE comic (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_5B7EA5AAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, comic_id INT NOT NULL, path VARCHAR(255) NOT NULL, alttext LONGTEXT DEFAULT NULL, createdon DATETIME NOT NULL, INDEX IDX_C53D045FD663094A (comic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, createdon DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comic ADD CONSTRAINT FK_5B7EA5AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FD663094A FOREIGN KEY (comic_id) REFERENCES comic (id)');
    }

    /** @noinspection SqlResolve
     * @noinspection SqlNoDataSourceInspection
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comic DROP FOREIGN KEY FK_5B7EA5AAA76ED395');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FD663094A');
        $this->addSql('DROP TABLE comic');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE user');
    }
}
