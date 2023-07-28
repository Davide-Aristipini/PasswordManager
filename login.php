<?php 
ini_set('session.cookie_lifetime', 86400);
session_start();

// Verifica se l'utente è autenticato
if (isset($_SESSION['token'])) {
    // L'utente è già autenticato, reindirizzalo alla dashboard
    header("Location: dashboard.php");
    exit();
} 

require_once 'config.php';
require_once 'PHPGangsta/GoogleAuthenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $codeArray = $_POST['code'];
    $code = implode('', $codeArray); // Unisci i singoli caratteri del codice 2FA in una stringa

    // Verifica se l'utente esiste nel database e se l'account è confermato
    $sql = "SELECT qr_code_secret, token FROM users WHERE email = ? AND is_confirmed = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $secret = $row['qr_code_secret'];
        $token = $row['token'];

        // Verifica il codice TOTP
        $checkResult = $ga->verifyCode($secret, $code, 2); // 2 = 2*30sec clock tolerance

        if ($checkResult) {
            // Codice TOTP corretto, imposta la variabile di sessione per l'utente loggato
            $_SESSION['token'] = $token;
            // Reindirizza l'utente alla dashboard dopo il login
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Codice 2FA non valido.";
        }
    } else {
        $error_message = "Email non trovata o account non confermato.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>SalvaPassword - Accedi</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="text-center mb-4">Portachiavi</h1>
                        <h2 class="text-center mb-4">Login</h2>

                        <!-- Card di errore o allerta -->
                        <?php if (isset($error_message)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php } ?>

                        <!-- Form di login -->
                        <form action="login.php" method="post">
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="code">Codice 2FA:</label>
                                <div class="input-group">
                                    <?php 
                                    // Crea i campi input per inserire il codice 2FA
                                    for($i = 0; $i < 6; $i++) {
                                        echo '<input type="number" id="code' . $i . '" name="code[]" min="0" max="9" class="form-control input-code mr-2" autocomplete="off">';
                                    }  ?>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Accedi</button>
                        </form>
                        <p class="text-center mt-3">Non hai ancora un account? <a href="register.php">Registrati</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Auto focus sul campo email all'avvio della pagina
            $('#email').focus();

            // Gestione dell'input del codice 2FA e passaggio automatico al campo successivo
            $('.input-code').keyup(function(e) {
                if ($(this).val().length == $(this).attr('maxlength')) {
                    if ($(this).next('.input-code').length > 0) {
                        $(this).next('.input-code').focus();
                    }
                }
            });

            var codeInput = $('input[name="code[]"]');
            codeInput.keydown(function(e) {
                if (e.keyCode == 8 || e.keyCode == 46) {
                    // Backspace o Delete premuto
                    if ($(this).val() == '') {
                        $(this).prev('.input-code').focus(); // Passa al campo di input precedente
                    }
                } else if (e.keyCode == 13) {
                    // Invio premuto
                    var allFilled = true;
                    codeInput.each(function() {
                        if ($(this).val() == '') {
                            allFilled = false;
                            return false; // Esci dal ciclo each se almeno un campo è vuoto
                        }
                    });

                    if (allFilled) {
                        // Se tutti i campi sono riempiti, invia il form
                        $('#form-2fa').submit();
                    }
                }
            });
        });
    </script>
</body>
</html>
