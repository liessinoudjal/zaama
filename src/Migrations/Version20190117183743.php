<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190117183743 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sortie (id INT AUTO_INCREMENT NOT NULL, organisateur_id INT DEFAULT NULL, type_sortie_id INT DEFAULT NULL, portable VARCHAR(10) DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, open_to VARCHAR(255) DEFAULT NULL, intitule VARCHAR(255) NOT NULL, entre_fille TINYINT(1) DEFAULT NULL, nb_personne_max INT DEFAULT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_3C3FD3F2D936B2FA (organisateur_id), INDEX IDX_3C3FD3F278B49843 (type_sortie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2D936B2FA FOREIGN KEY (organisateur_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F278B49843 FOREIGN KEY (type_sortie_id) REFERENCES type_sortie (id)');
    ;
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE villes_france_free (ville_id INT UNSIGNED AUTO_INCREMENT NOT NULL, ville_departement VARCHAR(3) DEFAULT NULL COLLATE utf8_general_ci, ville_slug VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci, ville_nom VARCHAR(45) DEFAULT NULL COLLATE utf8_general_ci, ville_nom_simple VARCHAR(45) DEFAULT NULL COLLATE utf8_general_ci, ville_nom_reel VARCHAR(45) DEFAULT NULL COLLATE utf8_general_ci, ville_nom_soundex VARCHAR(20) DEFAULT NULL COLLATE utf8_general_ci, ville_nom_metaphone VARCHAR(22) DEFAULT NULL COLLATE utf8_general_ci, ville_code_postal VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci, ville_commune VARCHAR(3) DEFAULT NULL COLLATE utf8_general_ci, ville_code_commune VARCHAR(5) NOT NULL COLLATE utf8_general_ci, ville_arrondissement SMALLINT UNSIGNED DEFAULT NULL, ville_canton VARCHAR(4) DEFAULT NULL COLLATE utf8_general_ci, ville_amdi SMALLINT UNSIGNED DEFAULT NULL, ville_population_2010 INT UNSIGNED DEFAULT NULL, ville_population_1999 INT UNSIGNED DEFAULT NULL, ville_population_2012 INT UNSIGNED DEFAULT NULL COMMENT \'approximatif\', ville_densite_2010 INT DEFAULT NULL, ville_surface DOUBLE PRECISION DEFAULT NULL, ville_longitude_deg DOUBLE PRECISION DEFAULT NULL, ville_latitude_deg DOUBLE PRECISION DEFAULT NULL, ville_longitude_grd VARCHAR(9) DEFAULT NULL COLLATE utf8_general_ci, ville_latitude_grd VARCHAR(8) DEFAULT NULL COLLATE utf8_general_ci, ville_longitude_dms VARCHAR(9) DEFAULT NULL COLLATE utf8_general_ci, ville_latitude_dms VARCHAR(8) DEFAULT NULL COLLATE utf8_general_ci, ville_zmin INT DEFAULT NULL, ville_zmax INT DEFAULT NULL, UNIQUE INDEX ville_code_commune_2 (ville_code_commune), UNIQUE INDEX ville_slug (ville_slug), INDEX ville_departement (ville_departement), INDEX ville_nom (ville_nom), INDEX ville_nom_reel (ville_nom_reel), INDEX ville_code_commune (ville_code_commune), INDEX ville_code_postal (ville_code_postal), INDEX ville_longitude_latitude_deg (ville_longitude_deg, ville_latitude_deg), INDEX ville_nom_soundex (ville_nom_soundex), INDEX ville_nom_metaphone (ville_nom_metaphone), INDEX ville_population_2010 (ville_population_2010), INDEX ville_nom_simple (ville_nom_simple), PRIMARY KEY(ville_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE sortie');
    }
}
