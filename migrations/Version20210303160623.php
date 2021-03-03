<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210303160623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Created licenses table';
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE licenses (
                uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
                user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\',
                product VARCHAR(255) NOT NULL COMMENT \'(DC2Type:license_product)\',
                type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:license_type)\',
                description VARCHAR(255) NOT NULL,
                options LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\',
                maximum_number_of_feeds INT DEFAULT 0 NOT NULL,
                number_of_activations_left INT DEFAULT 0 NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_7F320F3FA76ED395 (user_id),
                PRIMARY KEY(uuid)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        '
        );

        $this->addSql('ALTER TABLE licenses ADD CONSTRAINT FK_7F320F3FA76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE');
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE licenses');
    }
}
