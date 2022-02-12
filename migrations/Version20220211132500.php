<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220211132500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recursive_object_one (id INT AUTO_INCREMENT NOT NULL, sub_object_id INT NOT NULL, INDEX IDX_B0EF031531201095 (sub_object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recursive_object_two (id INT AUTO_INCREMENT NOT NULL, sub_object_id INT NOT NULL, INDEX IDX_DB490F8231201095 (sub_object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recursive_object_one ADD CONSTRAINT FK_B0EF031531201095 FOREIGN KEY (sub_object_id) REFERENCES recursive_object_two (id)');
        $this->addSql('ALTER TABLE recursive_object_two ADD CONSTRAINT FK_DB490F8231201095 FOREIGN KEY (sub_object_id) REFERENCES recursive_object_one (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recursive_object_two DROP FOREIGN KEY FK_DB490F8231201095');
        $this->addSql('ALTER TABLE recursive_object_one DROP FOREIGN KEY FK_B0EF031531201095');
        $this->addSql('DROP TABLE recursive_object_one');
        $this->addSql('DROP TABLE recursive_object_two');
        $this->addSql('ALTER TABLE role CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE role_with_annotation CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE login login VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_with_annotation CHANGE login login VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
