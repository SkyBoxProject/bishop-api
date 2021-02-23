<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210218170143 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Added email_verification_tokens table';
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE `email_verification_tokens` (
                token VARCHAR(180) NOT NULL,
                user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\',
                verified TINYINT(1) DEFAULT \'0\' NOT NULL,
                UNIQUE INDEX UNIQ_C81CA2ACA76ED395 (user_id),
                PRIMARY KEY(token)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');

        $this->addSql('ALTER TABLE `email_verification_tokens` ADD CONSTRAINT FK_C81CA2ACA76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id)');
        $this->addSql('ALTER TABLE users ADD email_verification_token VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9C4995C67 FOREIGN KEY (email_verification_token) REFERENCES `email_verification_tokens` (token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C4995C67 ON users (email_verification_token)');
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `users` DROP FOREIGN KEY FK_1483A5E9C4995C67');
        $this->addSql('DROP TABLE `email_verification_tokens`');
        $this->addSql('DROP INDEX UNIQ_1483A5E9C4995C67 ON `users`');
        $this->addSql('ALTER TABLE `users` DROP email_verification_token');
    }
}
