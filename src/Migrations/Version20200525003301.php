<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200525003301 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FBF850E54');
        $this->addSql('DROP INDEX IDX_AF86866FBF850E54 ON offre');
        $this->addSql('ALTER TABLE offre DROP postulers_id');
        $this->addSql('ALTER TABLE postuler ADD offre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE postuler ADD CONSTRAINT FK_8EC5A68D4CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)');
        $this->addSql('CREATE INDEX IDX_8EC5A68D4CC8505A ON postuler (offre_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offre ADD postulers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FBF850E54 FOREIGN KEY (postulers_id) REFERENCES postuler (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_AF86866FBF850E54 ON offre (postulers_id)');
        $this->addSql('ALTER TABLE postuler DROP FOREIGN KEY FK_8EC5A68D4CC8505A');
        $this->addSql('DROP INDEX IDX_8EC5A68D4CC8505A ON postuler');
        $this->addSql('ALTER TABLE postuler DROP offre_id');
    }
}
