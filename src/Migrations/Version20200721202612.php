<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200721202612 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE postuler_recruteur');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE postuler_recruteur (postuler_id INT NOT NULL, recruteur_id INT NOT NULL, INDEX IDX_86567BA9CFF603D (postuler_id), INDEX IDX_86567BABB0859F1 (recruteur_id), PRIMARY KEY(postuler_id, recruteur_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE postuler_recruteur ADD CONSTRAINT FK_86567BA9CFF603D FOREIGN KEY (postuler_id) REFERENCES postuler (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE postuler_recruteur ADD CONSTRAINT FK_86567BABB0859F1 FOREIGN KEY (recruteur_id) REFERENCES recruteur (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
