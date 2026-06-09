
-- NextAcademy — Base de données VERSION FINAL

CREATE DATABASE IF NOT EXISTS nextacademy_3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nextacademy_3;

--  Table utilisateurs 
CREATE TABLE utilisateurs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    prenom        VARCHAR(100) NOT NULL,
    nom           VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255) NOT NULL,
    telephone     VARCHAR(30),
    date_naissance DATE,
    adresse       VARCHAR(255),
    ville         VARCHAR(100),
    pays          VARCHAR(100) DEFAULT 'Maroc',
    bio           TEXT,
    avatar        VARCHAR(255),
    linkedin      VARCHAR(255),
    github        VARCHAR(255),
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table étudiants 
CREATE TABLE etudiants (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  INT NOT NULL,
    cne             VARCHAR(50) UNIQUE,
    cin             VARCHAR(50) UNIQUE,
    classe          VARCHAR(150),
    date_inscription DATE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

--  Table matières 
CREATE TABLE matieres (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(150) NOT NULL,
    coefficient TINYINT DEFAULT 1
);

--  Table notes 
CREATE TABLE notes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id INT NOT NULL,
    matiere     VARCHAR(150) NOT NULL,
    professeur  VARCHAR(150),
    controle1   DECIMAL(4,2),
    controle2   DECIMAL(4,2),
    examen      DECIMAL(4,2),
    moyenne     DECIMAL(4,2) AS (ROUND((COALESCE(controle1,0) + COALESCE(controle2,0) + COALESCE(examen,0)) / NULLIF((IF(controle1 IS NOT NULL,1,0)+IF(controle2 IS NOT NULL,1,0)+IF(examen IS NOT NULL,1,0)),0), 2)) STORED,
    semestre    VARCHAR(5) DEFAULT 'S1',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE
);

-- Table absences 
CREATE TABLE absences (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id   INT NOT NULL,
    date_abs      DATE NOT NULL,
    matiere       VARCHAR(150),
    professeur    VARCHAR(150),
    duree         VARCHAR(10) DEFAULT '2h',
    statut        VARCHAR(30) DEFAULT 'En attente',
    justificatif  VARCHAR(255),
    semestre      VARCHAR(5),
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE
);

-- Tables badges
CREATE TABLE badges (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id INT NOT NULL,
    libelle     VARCHAR(100),
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE
);
-- Tables planning
CREATE TABLE planning (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    matiere_id  INT NOT NULL,
    classe      VARCHAR(150),
    jour        VARCHAR(20),
    heure_debut TIME,
    salle       VARCHAR(50),
    FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE CASCADE
);

-- Table cours 
CREATE TABLE IF NOT EXISTS cours (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titre       VARCHAR(200) NOT NULL,
    description TEXT,
    professeur  VARCHAR(150),
    couleur_badge VARCHAR(20) DEFAULT 'primary',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);
 

-- DONNÉES DE TEST (a adapter selon les membres d'equipes)

INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, telephone, date_naissance, adresse, ville, pays, bio, linkedin, github)
VALUES ('Doha', 'Kchibel', 'doha.kchi18@gmail.com', '123456',
        '+212 6 73 64 00 85', '2005-11-18', '12 Rue Al Qods', 'Oujda', 'Maroc',
        'Passionnée par la digitalisation', 'linkedin.com/in/doha-kchibel', 'github.com/dohakchibel');

INSERT INTO etudiants (utilisateur_id, cne, cin, classe, date_inscription)
VALUES (1, 'F13320955', 'Ta168076', 'MGSI-3', '2023-09-01');

INSERT INTO matieres (nom, coefficient) VALUES
('Développement Web', 2), ('Base de Données', 2), ('Réseaux', 1),
('Algorithmique', 1), ('Anglais', 1), ('Français', 1);

INSERT INTO notes (etudiant_id, matiere, professeur, controle1, controle2, examen, semestre) VALUES
(1, 'Développement Web',  'Prof. Ouadoud',  15.0, 16.0, 17.0, 'S1'),
(1, 'Base de Données',    'Prof. Elhassani', 14.0, 17.0, 18.0, 'S1'),
(1, 'Réseaux',            'Prof. Oubaha',   12.0, 13.0, 13.0, 'S1'),
(1, 'Algorithmique',      'Prof. Benali',   11.0, 12.0, 14.0, 'S1'),
(1, 'Anglais',            'Prof. Hassan',   17.0, 18.0, 18.5, 'S1');

INSERT INTO absences (etudiant_id, date_abs, matiere, professeur, duree, statut, justificatif, semestre) VALUES
(1, '2026-02-10', 'Réseaux',         'Prof. Oubaha',    '2h', 'Justifiée',     'Certificat médical', 'S1'),
(1, '2026-04-18', 'Développement Web','Prof. Ouadoud',   '4h', 'Non justifiée', NULL,                  'S2');

INSERT INTO badges (etudiant_id, libelle) VALUES (1, 'Responsable'), (1, 'Projet Excellent');

INSERT INTO planning (matiere_id, classe, jour, heure_debut, salle) VALUES
(1, 'MGSI-3', 'Lundi',  '08:00:00', 'Salle AR7'),
(2, 'MGSI-3', 'Mardi',  '08:00:00', 'Salle AR7'),
(3, 'MGSI-3', 'Mardi',  '14:00:00', 'Salle AE9');


INSERT INTO cours (titre, description, professeur, couleur_badge) VALUES
('Développement Web', 'HTML, CSS, Bootstrap et JavaScript', 'Mohammed Ouadoud', 'primary'),
('Systèmes Réseaux', 'Architecture TCP/IP et adressage IP', 'Jawad Oubaha', 'success'),
('Base de Données', 'SQL, MySQL et conception MERISE', 'Mohammed Ouadoud', 'warning'),
('Management et Outils de Pilotage', 'Comptabilité générale et analytique', 'Bouchra Setta', 'danger');