CREATE DATABASE IF NOT EXISTS ac_ayar_todo_list;
USE ac_ayar_todo_list;

CREATE TABLE IF NOT EXISTS projets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(200) NOT NULL,
    couleur VARCHAR(7) DEFAULT '#007bff'
);

CREATE TABLE IF NOT EXISTS taches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    projet_id INT,
    titre VARCHAR(300) NOT NULL,
    description TEXT,
    priorite ENUM('basse', 'normale', 'haute') DEFAULT 'normale',
    statut ENUM('a_faire', 'en_cours', 'termine') DEFAULT 'a_faire',
    date_echeance DATE,
    ordre INT DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projet_id) REFERENCES projets(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS etiquettes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    couleur VARCHAR(7) DEFAULT '#6c757d'
);

CREATE TABLE IF NOT EXISTS taches_etiquettes (
    tache_id INT,
    etiquette_id INT,
    PRIMARY KEY (tache_id, etiquette_id),
    FOREIGN KEY (tache_id) REFERENCES taches(id) ON DELETE CASCADE,
    FOREIGN KEY (etiquette_id) REFERENCES etiquettes(id) ON DELETE CASCADE
);
