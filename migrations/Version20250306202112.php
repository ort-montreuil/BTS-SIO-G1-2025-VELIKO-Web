<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306202112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD station_id_depart BIGINT NOT NULL, ADD station_id_arrivee BIGINT NOT NULL, DROP id_station_depart, DROP id_station_arrivee');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955BF8D60FE FOREIGN KEY (station_id_depart) REFERENCES station (station_id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955122FC5E6 FOREIGN KEY (station_id_arrivee) REFERENCES station (station_id)');
        $this->addSql('CREATE INDEX IDX_42C84955BF8D60FE ON reservation (station_id_depart)');
        $this->addSql('CREATE INDEX IDX_42C84955122FC5E6 ON reservation (station_id_arrivee)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955BF8D60FE');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955122FC5E6');
        $this->addSql('DROP INDEX IDX_42C84955BF8D60FE ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955122FC5E6 ON reservation');
        $this->addSql('ALTER TABLE reservation ADD id_station_depart INT NOT NULL, ADD id_station_arrivee INT NOT NULL, DROP station_id_depart, DROP station_id_arrivee');
    }
}
