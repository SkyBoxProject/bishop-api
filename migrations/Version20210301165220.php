<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210301165220 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create feeds table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE feeds (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', url VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:feed_type)\', removed_description VARCHAR(255) NOT NULL, stop_words LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', added_city VARCHAR(255) NOT NULL, is_remove_last_image TINYINT(1) DEFAULT \'0\' NOT NULL, text_after_description VARCHAR(255) NOT NULL, is_exclude_out_of_stock_items TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5A29F52FA76ED395 (user_id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feeds ADD CONSTRAINT FK_5A29F52FA76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE feeds');
    }
}
