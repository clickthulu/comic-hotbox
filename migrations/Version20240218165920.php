<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218165920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hot_box_comic DROP FOREIGN KEY FK_C3FB2D90D663094A');
        $this->addSql('ALTER TABLE hot_box_comic DROP FOREIGN KEY FK_C3FB2D903632BE2F');
        $this->addSql('DROP TABLE hot_box_comic');
        $this->addSql('ALTER TABLE hot_box ADD rotation_frequency INT NOT NULL');
        $this->addSql('ALTER TABLE rotation ADD start DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hot_box_comic (hot_box_id INT NOT NULL, comic_id INT NOT NULL, INDEX IDX_C3FB2D903632BE2F (hot_box_id), INDEX IDX_C3FB2D90D663094A (comic_id), PRIMARY KEY(hot_box_id, comic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE hot_box_comic ADD CONSTRAINT FK_C3FB2D90D663094A FOREIGN KEY (comic_id) REFERENCES comic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hot_box_comic ADD CONSTRAINT FK_C3FB2D903632BE2F FOREIGN KEY (hot_box_id) REFERENCES hot_box (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hot_box DROP rotation_frequency');
        $this->addSql('ALTER TABLE rotation DROP start');
    }
}
