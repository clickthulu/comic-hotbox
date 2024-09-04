<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240904005740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE webring ADD navigation_width INT NOT NULL, ADD ring_width INT NOT NULL, ADD ring_height INT NOT NULL, DROP image_width, DROP image_height, DROP button_width');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE webring ADD image_width INT NOT NULL, ADD image_height INT NOT NULL, ADD button_width INT NOT NULL, DROP navigation_width, DROP ring_width, DROP ring_height');
    }
}
