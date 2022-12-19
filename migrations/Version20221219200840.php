<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221219200840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD multi TINYINT(1) NOT NULL, ADD played TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE inscription_date inscription_date DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP multi, DROP played');
        $this->addSql('ALTER TABLE user CHANGE inscription_date inscription_date DATE DEFAULT \'2023-01-01\' NOT NULL');
    }
}
