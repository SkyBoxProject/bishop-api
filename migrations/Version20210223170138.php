<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210223170138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update cascade';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_verification_tokens DROP FOREIGN KEY FK_C81CA2ACA76ED395');
        $this->addSql('ALTER TABLE email_verification_tokens ADD CONSTRAINT FK_C81CA2ACA76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9C4995C67');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9C4995C67 FOREIGN KEY (email_verification_token) REFERENCES `email_verification_tokens` (token) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `email_verification_tokens` DROP FOREIGN KEY FK_C81CA2ACA76ED395');
        $this->addSql('ALTER TABLE `email_verification_tokens` ADD CONSTRAINT FK_C81CA2ACA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE `users` DROP FOREIGN KEY FK_1483A5E9C4995C67');
        $this->addSql('ALTER TABLE `users` ADD CONSTRAINT FK_1483A5E9C4995C67 FOREIGN KEY (email_verification_token) REFERENCES email_verification_tokens (token) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
