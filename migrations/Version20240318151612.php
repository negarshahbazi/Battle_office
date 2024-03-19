<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240318151612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, photo_id INT DEFAULT NULL, command_id INT DEFAULT NULL, price INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D34A04AD7E9E4C8C (photo_id), INDEX IDX_D34A04AD33E1689A (command_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD33E1689A FOREIGN KEY (command_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993987E9E4C8C');
        $this->addSql('DROP INDEX UNIQ_F52993987E9E4C8C ON `order`');
        $this->addSql('ALTER TABLE `order` DROP photo_id, DROP product, DROP price');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7E9E4C8C');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD33E1689A');
        $this->addSql('DROP TABLE product');
        $this->addSql('ALTER TABLE `order` ADD photo_id INT DEFAULT NULL, ADD product VARCHAR(255) NOT NULL, ADD price INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993987E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52993987E9E4C8C ON `order` (photo_id)');
    }
}
