<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221220184634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE challenge_period (challenge_id INT NOT NULL, period_id INT NOT NULL, INDEX IDX_3605BD0F98A21AC6 (challenge_id), INDEX IDX_3605BD0FEC8B7ADE (period_id), PRIMARY KEY(challenge_id, period_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE challenge_period ADD CONSTRAINT FK_3605BD0F98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE challenge_period ADD CONSTRAINT FK_3605BD0FEC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge_period DROP FOREIGN KEY FK_3605BD0F98A21AC6');
        $this->addSql('ALTER TABLE challenge_period DROP FOREIGN KEY FK_3605BD0FEC8B7ADE');
        $this->addSql('DROP TABLE challenge_period');
    }
}
