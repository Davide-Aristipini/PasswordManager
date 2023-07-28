<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';

// Funzione per eliminare una password dal database
function deletePassword($token, $passwordId) {
    global $conn;

    $sql = "DELETE FROM passwords WHERE token = ? AND id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $token, $passwordId);
    $stmt->execute();

    return $stmt->affected_rows > 0;
}

if (isset($_GET['token']) && isset($_GET['id'])) {
    $token = $_GET['token'];
    $passwordId = $_GET['id'];

    // Verifica se l'utente è autorizzato a eliminare la password
    if ($token === $_SESSION['token']) {
        // Elimina la password dal database
        if (deletePassword($token, $passwordId)) {
            // Reindirizza l'utente alla pagina della dashboard dopo l'eliminazione
            header("Location: dashboard.php");
            exit();
        }
    }
}

// Se il controllo o l'eliminazione non sono andati a buon fine, reindirizza alla dashboard
header("Location: dashboard.php");
exit();
?>
