<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251111220000AddFiles extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image fields to Dish and file fields to Order';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_file (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, original_name VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, file_type VARCHAR(50) NOT NULL, file_size INT NOT NULL, uploaded_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5D70F5C38D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_file ADD CONSTRAINT FK_5D70F5C38D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE dish ADD image_name VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_file DROP FOREIGN KEY FK_5D70F5C38D9F6D38');
        $this->addSql('DROP TABLE order_file');
        $this->addSql('ALTER TABLE dish DROP image_name, DROP updated_at');
    }
}