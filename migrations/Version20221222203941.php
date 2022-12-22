<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221222203941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE submission (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, challenge_id INT NOT NULL, period_id INT NOT NULL, submission_date DATETIME NOT NULL, validation_date DATETIME NOT NULL, valid TINYINT(1) NOT NULL, INDEX IDX_DB055AF3A76ED395 (user_id), INDEX IDX_DB055AF398A21AC6 (challenge_id), INDEX IDX_DB055AF3EC8B7ADE (period_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF398A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3EC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF3A76ED395');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF398A21AC6');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF3EC8B7ADE');
        $this->addSql('DROP TABLE submission');
    }
}
