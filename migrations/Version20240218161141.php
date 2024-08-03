<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218161141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rotation (id INT AUTO_INCREMENT NOT NULL, hotbox_id INT NOT NULL, comic_id INT NOT NULL, ordinal INT NOT NULL, INDEX IDX_297C98F1798BABA1 (hotbox_id), INDEX IDX_297C98F1D663094A (comic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rotation ADD CONSTRAINT FK_297C98F1798BABA1 FOREIGN KEY (hotbox_id) REFERENCES hot_box (id)');
        $this->addSql('ALTER TABLE rotation ADD CONSTRAINT FK_297C98F1D663094A FOREIGN KEY (comic_id) REFERENCES comic (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rotation DROP FOREIGN KEY FK_297C98F1798BABA1');
        $this->addSql('ALTER TABLE rotation DROP FOREIGN KEY FK_297C98F1D663094A');
        $this->addSql('DROP TABLE rotation');
    }
}
