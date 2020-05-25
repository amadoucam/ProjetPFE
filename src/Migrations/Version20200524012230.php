<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200524012230 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offre ADD postuler_id INT DEFAULT NULL, CHANGE postulers_id postulers_id INT NOT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F9CFF603D FOREIGN KEY (postuler_id) REFERENCES postuler (id)');
        $this->addSql('CREATE INDEX IDX_AF86866F9CFF603D ON offre (postuler_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F9CFF603D');
        $this->addSql('DROP INDEX IDX_AF86866F9CFF603D ON offre');
        $this->addSql('ALTER TABLE offre DROP postuler_id, CHANGE postulers_id postulers_id INT DEFAULT NULL');
    }
}
