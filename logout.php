<?php
// Inizializza la sessione (se non già inizializzata)
session_start();

// Cancella tutte le variabili di sessione
session_unset();

// Distruggi la sessione
session_destroy();

// Reindirizza l'utente alla pagina di login dopo il logout (puoi modificare la destinazione come preferisci)
header("Location: login.php");
exit();
?>
