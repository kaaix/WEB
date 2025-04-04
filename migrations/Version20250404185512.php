<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250404185512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE panier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_24CC0DF2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_24CC0DF2A76ED395 ON panier (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE panier_produit (panier_id INTEGER NOT NULL, produit_id INTEGER NOT NULL, PRIMARY KEY(panier_id, produit_id), CONSTRAINT FK_D31F28A6F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D31F28A6F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D31F28A6F77D927C ON panier_produit (panier_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D31F28A6F347EFB ON panier_produit (produit_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__user AS SELECT id, pays_id, login, roles, password, nom, prenom, date_naissance, actif FROM user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pays_id INTEGER NOT NULL, login VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, actif BOOLEAN NOT NULL, CONSTRAINT FK_8D93D649A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO user (id, pays_id, login, roles, password, nom, prenom, date_naissance, actif) SELECT id, pays_id, login, roles, password, nom, prenom, date_naissance, actif FROM __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D649A6E44244 ON user (pays_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649AA08CB10 ON user (login)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE panier
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE panier_produit
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__user AS SELECT id, pays_id, login, roles, password, nom, prenom, date_naissance, actif FROM user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pays_id INTEGER DEFAULT NULL, login VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, actif BOOLEAN NOT NULL, CONSTRAINT FK_8D93D649A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO user (id, pays_id, login, roles, password, nom, prenom, date_naissance, actif) SELECT id, pays_id, login, roles, password, nom, prenom, date_naissance, actif FROM __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649AA08CB10 ON user (login)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D649A6E44244 ON user (pays_id)
        SQL);
    }
}
