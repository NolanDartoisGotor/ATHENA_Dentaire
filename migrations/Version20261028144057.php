<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028144057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_message (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(100) NOT NULL, first_name VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, subject VARCHAR(200) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE opening_hour (id INT AUTO_INCREMENT NOT NULL, day_label VARCHAR(80) NOT NULL, hours VARCHAR(120) NOT NULL, position INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE page_section (id INT AUTO_INCREMENT NOT NULL, section_key VARCHAR(80) NOT NULL, title VARCHAR(150) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, body LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_D713917A514C78FB (section_key), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, icon VARCHAR(255) DEFAULT NULL, position INT NOT NULL, published TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE site_settings (id INT NOT NULL, organization_name VARCHAR(255) NOT NULL, tagline VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone VARCHAR(30) DEFAULT NULL, emergency_phone VARCHAR(30) DEFAULT NULL, contact_email VARCHAR(180) NOT NULL, form_recipient_email VARCHAR(180) DEFAULT NULL, google_maps_embed_url VARCHAR(500) DEFAULT NULL, hero_image VARCHAR(255) DEFAULT NULL, map_image VARCHAR(255) DEFAULT NULL, access_plan_image VARCHAR(255) DEFAULT NULL, assistants_list LONGTEXT DEFAULT NULL, contact_form_notice LONGTEXT DEFAULT NULL, footer_text VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team_member (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(150) NOT NULL, role VARCHAR(150) NOT NULL, photo VARCHAR(255) DEFAULT NULL, position INT NOT NULL, published TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE technical_facility (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, layout VARCHAR(20) NOT NULL, position INT NOT NULL, published TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(100) NOT NULL, email_auth_enabled TINYINT DEFAULT 1 NOT NULL, auth_code VARCHAR(10) DEFAULT NULL, auth_code_expires_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_USER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE contact_message');
        $this->addSql('DROP TABLE opening_hour');
        $this->addSql('DROP TABLE page_section');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE site_settings');
        $this->addSql('DROP TABLE team_member');
        $this->addSql('DROP TABLE technical_facility');
        $this->addSql('DROP TABLE `user`');
    }
}
