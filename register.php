<?php session_start();

// Verifica se l'utente è autenticato
if (isset($_SESSION['token'])) {
    // L'utente non è autenticato, reindirizzalo alla pagina di login
    header("Location: dashboard.php");
    exit();
} 
require_once 'config.php';
require_once 'sendmail.php';

function generateToken() {
    $token = bin2hex(random_bytes(32)); // Genera un token di 32 byte in esadecimale
    return $token;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = password_hash("password", PASSWORD_BCRYPT); // Assicurati di proteggere la password con hash!
    // Verifica se l'email esiste già nel database
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // L'email esiste già nel database, esegui azioni di gestione di questo caso (es. avviso all'utente)
        $error_message = "Questa email è già registrata. Prova a effettuare il login o utilizza un'altra email.";
    } else {
        // Genera un token univoco
        $token = generateToken();

        // Inserisci l'utente nel database con il token generato
        $sql = "INSERT INTO users (email, password, token) VALUES ('$email', '$password', '$token')";
        if ($conn->query($sql) === TRUE) {
            // Invia la mail di conferma con il link a confirm_account.php e il token generato
            $to = $email;
            $subject = "Conferma Account SalvaPassword";
            $message = "Ciao! Per confermare il tuo account, clicca sul seguente link: \n";
            $message .= "http://localhost/SalvaPassword/confirm_account.php?email=" . urlencode($email) . "&token=" . urlencode($token);

            if (sendEmail($to, $subject, $message)) {
                $success_message = "Grazie per esserti registrato! Controlla la tua email per confermare l'account.";
            } else {
                $error_message = "Errore durante l'invio della mail di conferma. Riprova più tardi.";
            }
        } else {
            $error_message = "Errore durante la registrazione: " . $conn->error;
        }
            }
        }

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>SalvaPassword - Registrazione</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>SalvaPassword - Registrazione</h1>

        <!-- Card di errore o allerta -->
        <?php if (isset($success_message)) { ?>
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Successo</h5>
                    <p class="card-text"><?php echo $success_message; ?></p>
                </div>
            </div>
        <?php } ?>

        <!-- Form di registrazione -->
        <form id="registrationForm" action="register.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrati</button>
        </form>
        <p>Sei già registrato? <a href="login.php">Effettua il login</a></p>
        <div id="errorAlert" class="alert alert-danger" style="display: none;">
            <?php
            if (isset($error_message)) {
                echo $error_message;
            }
            ?>
        </div>
    </div>

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
    </script>
</body>
</html>
