<?php
// Configurazione del database
define('DB_HOST', 'localhost'); // Indirizzo del database (solitamente localhost)
define('DB_USER', 'username'); // Nome utente del database
define('DB_PASS', 'password'); // Password del database
define('DB_NAME', 'salvapassword'); // Nome del database

// Connettiti al database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica la connessione
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}
?>
