-- ================================
-- Schema del database Archivio Film
-- ================================

CREATE DATABASE IF NOT EXISTS archivio_film CHARACTER SET utf8mb4;
USE archivio_film;

CREATE TABLE generi (
    id_genere INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE autori (
    id_autore INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(60) NOT NULL,
    cognome VARCHAR(60) NOT NULL,
    nazionalità VARCHAR(60) ,
    data:nascita DATE
);

CREATE TABLE film (
    id_film INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(150) NOT NULL,
    anno_uscita YEAR,
    durata_minuti INT,
    trama TEXT,
    locandina VARCHAR(255),
    id_autore INT,
    data_inserimento DATETIME DEAFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_autore) REFERENCES autori (id_autore)
    ON DELETE SET NULL
);
    
    CREATE TABLE film_generi (
        id_film INT NOT NULL,
        id_genere INT NOT NULL,
        PRIMARY KEY (id_film, id_genere),
        FOREIGN KEY (id_film) REFERENCES film(id_film) ON DELETE CASCADE,
        FOREIGN KEY (id_genere) REFERENCES generi(id_genere) ON DELETE CASCADE
    );
    -- Dati di prova
INSERT INTO generi (nome) VALUES
('Commedia'), ('Horror'), ('Fantascienza'), ('Drammatico'), ('Animazione');
 
INSERT INTO autori (nome, cognome, nazionalita, data_nascita) VALUES
('Christopher', 'Nolan', 'Britannica', '1970-07-30'),
('Hayao', 'Miyazaki', 'Giapponese', '1941-01-05');
 
INSERT INTO film (titolo, anno_uscita, durata_minuti, trama, id_autore) VALUES
('Inception', 2010, 148, 'Un ladro capace di entrare nei sogni altrui riceve l''incarico opposto: impiantare un''idea.', 1),
('La città incantata', 2001, 125, 'Una bambina si ritrova intrappolata in un mondo popolato da spiriti.', 2);
 
INSERT INTO film_generi (id_film, id_genere) VALUES
(1, 3),
(2, 5),
(2, 3);
)