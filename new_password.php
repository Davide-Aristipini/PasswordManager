<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';

// Verifica se l'utente è loggato, altrimenti reindirizzalo alla pagina di login
if (!isset($_SESSION['token'])) {
    header("Location: login.php");
    exit();
}

// Funzione per inserire una nuova password nel database
function insertNewPassword($token, $title, $username, $password, $description) {
    global $conn;
    
    // Crittografa la password prima di salvarla nel database
    // Assumendo che $encryptionKey sia la chiave segreta per la crittografia
    // Sostituisci questa parte con la tua funzione di crittografia reale
    $encryptedPassword = $password;

    $sql = "INSERT INTO passwords (token, title, username, password, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $token, $title, $username, $encryptedPassword, $description);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        return true;
    } else {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $description = $_POST['description'];

    // Verifica se tutti i campi sono stati compilati
    if (empty($title) || empty($username) || empty($password)) {
        $error_message = "Compila tutti i campi obbligatori.";
    } else {
        // Inserisci la nuova password nel database
        $token = $_SESSION['token'];
        if (insertNewPassword($token, $title, $username, $password, $description)) {
            // Reindirizza l'utente alla pagina della dashboard dopo il salvataggio
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Si è verificato un errore durante il salvataggio della password. Riprova.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>SalvaPassword - Nuova Password</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?> <!-- Includi l'header -->

    <div class="container">
        <h1>Nuova Password</h1>

        <!-- Card per inserire la nuova password -->
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <form action="new_password.php" method="post">
                            <div class="form-group">
                                <label for="title">Sito Web *</label>
                                <input type="text" class="form-control" id="title" name="title" required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="username">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="password">Password *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary show-password" type="button" onclick="togglePasswordVisibility()"><i class="fa fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Descrizione</label>
                                <textarea class="form-control" id="description" name="description" rows="3" autocomplete="off"></textarea>
                            </div>
                            <div class="row ml-0">
                                <button type="submit" class="btn btn-primary">Salva Password</button>
                                <a href="dashboard.php" class="btn btn-secondary ml-2">Torna alla Dashboard</a>
                            </div>   
                        </form>
                        <?php if (isset($error_message)) { ?>
                            <p class="text-danger"><?php echo $error_message; ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        // Funzione per mostrare o nascondere la password
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var passwordType = passwordInput.getAttribute("type");
            passwordInput.setAttribute("type", passwordType === "password" ? "text" : "password");

            var eyeIcon = document.querySelector(".show-password i");
            if (passwordType === "password") {
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
