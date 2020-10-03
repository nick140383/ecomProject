<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200930193614 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D438F5B63 FOREIGN KEY (mode_paiement_id) REFERENCES mode_paiement (id)');
        $this->addSql('ALTER TABLE fournisseur ADD CONSTRAINT FK_369ECA32A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('DROP INDEX id_2 ON ligne_commande');
        $this->addSql('DROP INDEX id ON ligne_commande');
        $this->addSql('DROP INDEX id_3 ON ligne_commande');
        $this->addSql('ALTER TABLE ligne_commande ADD quantite_retourne INT NOT NULL');
        $this->addSql('ALTER TABLE marque CHANGE fournisseur_id fournisseur_id INT NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B7841876312859 FOREIGN KEY (modele_chaussure_id) REFERENCES modele_chaussure (id)');
        $this->addSql('ALTER TABLE role_client ADD CONSTRAINT FK_6C62E5E4D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_client ADD CONSTRAINT FK_6C62E5E419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock DROP INDEX IDX_4B365660FF25611A, ADD UNIQUE INDEX UNIQ_4B365660FF25611A (taille_id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B36566076312859 FOREIGN KEY (modele_chaussure_id) REFERENCES modele_chaussure (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660FF25611A FOREIGN KEY (taille_id) REFERENCES taille (id)');
        $this->addSql('ALTER TABLE taille ADD CONSTRAINT FK_76508B38F5B0655D FOREIGN KEY (modele_chaussures_id) REFERENCES modele_chaussure (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE roles roles VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D438F5B63');
        $this->addSql('ALTER TABLE fournisseur DROP FOREIGN KEY FK_369ECA32A73F0036');
        $this->addSql('ALTER TABLE ligne_commande DROP quantite_retourne');
        $this->addSql('CREATE INDEX id_2 ON ligne_commande (id)');
        $this->addSql('CREATE UNIQUE INDEX id ON ligne_commande (id)');
        $this->addSql('CREATE UNIQUE INDEX id_3 ON ligne_commande (id)');
        $this->addSql('ALTER TABLE marque CHANGE fournisseur_id fournisseur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B7841876312859');
        $this->addSql('ALTER TABLE role_client DROP FOREIGN KEY FK_6C62E5E4D60322AC');
        $this->addSql('ALTER TABLE role_client DROP FOREIGN KEY FK_6C62E5E419EB6921');
        $this->addSql('ALTER TABLE stock DROP INDEX UNIQ_4B365660FF25611A, ADD INDEX IDX_4B365660FF25611A (taille_id)');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B36566076312859');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660FF25611A');
        $this->addSql('ALTER TABLE taille DROP FOREIGN KEY FK_76508B38F5B0655D');
    }
}
