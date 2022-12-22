<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221222205620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE submission_message_image (id INT AUTO_INCREMENT NOT NULL, submission_message_id INT NOT NULL, image VARCHAR(120) NOT NULL, INDEX IDX_7BE8B5E9FC6DBB8 (submission_message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE submission_message_image ADD CONSTRAINT FK_7BE8B5E9FC6DBB8 FOREIGN KEY (submission_message_id) REFERENCES submission_message (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE submission_message_image DROP FOREIGN KEY FK_7BE8B5E9FC6DBB8');
        $this->addSql('DROP TABLE submission_message_image');
    }
}
