<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// config/db.php
// Parametri di connessione al database.
// Con l'installazione standard di XAMPP, l'utente di default è "root" senza password.
$host = "localhost";
$utente = "root";
$password = "";
$nome_db = "archivio_film";

$conn = mysqli_connect($host, $utente, $password, $nome_db);

if(!$conn) {
    die("Connesione al database fallita : " . mysqli_connect_error());

}

// Impostiamo il set di caratteri della connessione, coerente con quello del database (utf8mb4),
// per evitare problemi con lettere accentate e caratteri speciali.
mysqli_set_charset($conn, "utf8mb4");