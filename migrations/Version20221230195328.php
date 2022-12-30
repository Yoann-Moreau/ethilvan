<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221230195328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ranking_position (id INT AUTO_INCREMENT NOT NULL, ranking_id INT NOT NULL, user_id INT NOT NULL, position INT NOT NULL, points INT NOT NULL, INDEX IDX_EB43BDE520F64684 (ranking_id), INDEX IDX_EB43BDE5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ranking_position ADD CONSTRAINT FK_EB43BDE520F64684 FOREIGN KEY (ranking_id) REFERENCES ranking (id)');
        $this->addSql('ALTER TABLE ranking_position ADD CONSTRAINT FK_EB43BDE5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ranking_position DROP FOREIGN KEY FK_EB43BDE520F64684');
        $this->addSql('ALTER TABLE ranking_position DROP FOREIGN KEY FK_EB43BDE5A76ED395');
        $this->addSql('DROP TABLE ranking_position');
    }
}
