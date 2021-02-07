<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210207161744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create cron_job and cron_job_result tables';
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE cron_job (
                id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
                command VARCHAR(255) NOT NULL,
                arguments VARCHAR(255) DEFAULT NULL,
                description VARCHAR(255) DEFAULT NULL,
                running_instances INT UNSIGNED DEFAULT 0 NOT NULL,
                max_instances INT UNSIGNED DEFAULT 1 NOT NULL,
                number INT UNSIGNED DEFAULT 1 NOT NULL,
                period VARCHAR(255) NOT NULL,
                last_use DATETIME DEFAULT NULL,
                next_run DATETIME NOT NULL,
                enable TINYINT(1) DEFAULT \'1\' NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL, PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');

        $this->addSql('
            CREATE TABLE cron_job_result (
                id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
                cron_job_id BIGINT UNSIGNED NOT NULL,
                run_at DATETIME NOT NULL,
                run_time DOUBLE PRECISION NOT NULL,
                status_code INT NOT NULL,
                output LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_2CD346EE79099ED8 (cron_job_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');

        $this->addSql('ALTER TABLE cron_job_result ADD CONSTRAINT FK_2CD346EE79099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_job (id)');
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cron_job_result DROP FOREIGN KEY FK_2CD346EE79099ED8');
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_job_result');
    }
}
