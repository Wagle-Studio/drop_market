<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250101102652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, wave_id INT NOT NULL, user_id INT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, ulid BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', UNIQUE INDEX UNIQ_F5299398C288C859 (ulid), INDEX IDX_F52993989461E358 (wave_id), INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_reset_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_C5D0A95AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, price_ttc NUMERIC(10, 2) NOT NULL, slug VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_D34A04AD989D9B62 (slug), INDEX IDX_D34A04AD4D16C4DD (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, postal_code VARCHAR(10) NOT NULL, slug VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, ulid BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', UNIQUE INDEX UNIQ_AC6A4CA2989D9B62 (slug), UNIQUE INDEX UNIQ_AC6A4CA2C288C859 (ulid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status_wave (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(150) NOT NULL, const VARCHAR(150) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, lastname VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, ulid BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', UNIQUE INDEX UNIQ_8D93D649C288C859 (ulid), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_shop (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, shop_id INT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_D6EB006BA76ED395 (user_id), INDEX IDX_D6EB006B4D16C4DD (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `wave` (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, status_id INT NOT NULL, start DATETIME NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, ulid BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', UNIQUE INDEX UNIQ_DA04AD89C288C859 (ulid), INDEX IDX_DA04AD894D16C4DD (shop_id), INDEX IDX_DA04AD896BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989461E358 FOREIGN KEY (wave_id) REFERENCES `wave` (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE password_reset_request ADD CONSTRAINT FK_C5D0A95AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE user_shop ADD CONSTRAINT FK_D6EB006BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_shop ADD CONSTRAINT FK_D6EB006B4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE `wave` ADD CONSTRAINT FK_DA04AD894D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE `wave` ADD CONSTRAINT FK_DA04AD896BF700BD FOREIGN KEY (status_id) REFERENCES status_wave (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989461E358');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE password_reset_request DROP FOREIGN KEY FK_C5D0A95AA76ED395');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD4D16C4DD');
        $this->addSql('ALTER TABLE user_shop DROP FOREIGN KEY FK_D6EB006BA76ED395');
        $this->addSql('ALTER TABLE user_shop DROP FOREIGN KEY FK_D6EB006B4D16C4DD');
        $this->addSql('ALTER TABLE `wave` DROP FOREIGN KEY FK_DA04AD894D16C4DD');
        $this->addSql('ALTER TABLE `wave` DROP FOREIGN KEY FK_DA04AD896BF700BD');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE password_reset_request');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE status_wave');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_shop');
        $this->addSql('DROP TABLE `wave`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
