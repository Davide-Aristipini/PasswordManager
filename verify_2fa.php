<?php session_start();

// Verifica se l'utente è autenticato
if (isset($_SESSION['token'])) {
    // L'utente non è autenticato, reindirizzalo alla pagina di login
    header("Location: dashboard.php");
    exit();
} ?>
<!DOCTYPE html>
<html>
<head>
    <title>SalvaPassword - Registrazione</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php
require_once 'config.php';
require_once 'PHPGangsta/GoogleAuthenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();

if (isset($_POST['code']) && isset($_POST['email']) && isset($_POST['secret'])) {
    $code = $_POST['code'];
    $email = $_POST['email'];
    $secret = $_POST['secret'];
    // Recupera il secret dell'utente
    $sql = "SELECT qr_code_secret FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica il codice TOTP
        $checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
        if ($checkResult) {
            // Il codice è corretto, imposta l'utente come confermato
            $sql = "UPDATE users SET is_confirmed = 1, qr_code_secret = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $secret, $email);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo '<div class="container">';
                echo '<p class="text-success">Account confermato con successo. Verrai reindirizzato alla pagina di login in <span id="countdown">5</span> secondi.</p>';
                echo '<button id="loginButton" class="btn btn-primary" disabled>Vai alla schermata di login</button>';
                echo '</div>';
            } else {
                echo '<div class="container">';
                echo '<p class="text-danger">Si è verificato un errore durante l\'aggiornamento del tuo account. Riprova.</p>';
                echo '</div>';
            }
        } else {
            echo '<div class="container">';
            echo '<p class="text-danger">Codice di verifica non valido.</p>';
            echo '</div>';
        }
    } else {
        echo '<div class="container">';
        echo '<p class="text-danger">Email non valida o account non trovato.</p>';
        echo '</div>';
    }
} else {
    echo '<div class="container">';
    echo '<p class="text-danger">Dati di verifica non validi o mancanti.</p>';
    echo '</div>';
}

$conn->close();
?>



    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        // Funzione per mostrare l'alert rosso
        function showErrorAlert() {
            var errorAlert = document.getElementById("errorAlert");
            errorAlert.style.display = "block";
        }

        // Controlla se l'alert deve essere mostrato al caricamento della pagina
        window.onload = function() {
            <?php if (isset($error_message)) { ?>
                showErrorAlert();
            <?php } ?>
        };

        var countdownElement = document.getElementById('countdown');
        if (countdownElement) {
            var countdownValue = parseInt(countdownElement.textContent);
            var loginButton = document.getElementById('loginButton');

            var countdownInterval = setInterval(function() {
                countdownValue--;
                if (countdownValue <= 0) {
                    // Abilita il pulsante di login e interrompi il countdown
                    loginButton.disabled = false;
                    clearInterval(countdownInterval);

                    // Redirezione alla pagina di login
                    window.location.href = "login.php";
                } else {
                    // Aggiorna il countdown
                    countdownElement.textContent = countdownValue;
                }
            }, 1000);
        }
    </script>
</body>
</html>