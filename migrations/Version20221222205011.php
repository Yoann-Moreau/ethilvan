<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221222205011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE submission_message (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, submission_id INT NOT NULL, message_date DATETIME NOT NULL, message LONGTEXT NOT NULL, INDEX IDX_9A5CABA9A76ED395 (user_id), INDEX IDX_9A5CABA9E1FD4933 (submission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE submission_message ADD CONSTRAINT FK_9A5CABA9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE submission_message ADD CONSTRAINT FK_9A5CABA9E1FD4933 FOREIGN KEY (submission_id) REFERENCES submission (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE submission_message DROP FOREIGN KEY FK_9A5CABA9A76ED395');
        $this->addSql('ALTER TABLE submission_message DROP FOREIGN KEY FK_9A5CABA9E1FD4933');
        $this->addSql('DROP TABLE submission_message');
    }
}
