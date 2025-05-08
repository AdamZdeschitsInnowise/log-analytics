<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250506175517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, service_name VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, request_info VARCHAR(255) DEFAULT NULL, status_code INT NOT NULL, INDEX idx_serviceName (service_name), INDEX idx_timestamp (timestamp), INDEX idx_statusCode (status_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE log_file (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, file_offset BIGINT NOT NULL, UNIQUE INDEX UNIQ_9DF1D865B548B0F (path), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE log
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE log_file
        SQL);
    }
}
