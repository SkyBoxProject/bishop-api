<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210206153509 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create refresh_tokens table';
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE refresh_tokens (
                id INT AUTO_INCREMENT NOT NULL,
                refresh_token VARCHAR(128) NOT NULL,
                username VARCHAR(255) NOT NULL,
                valid DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');

        $this->addSql('ALTER TABLE users RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_1483A5E9E7927C74');
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('ALTER TABLE `users` RENAME INDEX uniq_1483a5e9e7927c74 TO UNIQ_8D93D649E7927C74');
    }
}
