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

// Funzione per ottenere il link per modificare la password
function getEditPasswordLink($token, $passwordId) {
    return "edit_password.php?token=" . urlencode($token) . "&id=" . urlencode($passwordId);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_SESSION['token'];
    $passwordId = $_POST['password_id'];
    $title = $_POST['title'];
    $username = $_POST['username'];
    $password = $_POST['password']; // La password non è ancora criptata
    $description = $_POST['description'];

    // Aggiorna la password nel database
    $sql = "UPDATE passwords SET title = ?, username = ?, password = ?, description = ? WHERE token = ? AND id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $username, $password, $description, $token, $passwordId);
    if ($stmt->execute()) {
        // Reindirizza l'utente alla pagina dashboard dopo l'aggiornamento
        header("Location: dashboard.php");
        exit();
    } else {
        // Errore durante l'aggiornamento della password, gestisci l'errore
        $error_message = "Si è verificato un errore durante l'aggiornamento della password. Riprova più tardi.";
    }
}

// Recupera i dati della password da modificare dal database
$token = $_SESSION['token'];
$passwordId = $_GET['id'];
$sql = "SELECT * FROM passwords WHERE token = ? AND id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $token, $passwordId);
$stmt->execute();
$result = $stmt->get_result();

$password = array();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $password = array(
        'id' => $row['id'],
        'title' => $row['title'],
        'username' => $row['username'],
        'password' => $row['password'],
        'description' => $row['description']
    );
} else {
    // Password non trovata, gestisci l'errore
    $error_message = "Password non trovata.";
}

include 'header.php';
?>
    <div class="container mt-4">
        <h1>Modifica Password</h1>

        <!-- Card di errore o allerta -->
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <!-- Form di modifica password -->
        <?php if (!empty($password)) { ?>
            <form action="edit_password.php" method="post">
                <input type="hidden" name="password_id" value="<?php echo $password['id']; ?>">
                <div class="form-group">
                    <label for="title">Sito Web:</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $password['title']; ?>" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $password['username']; ?>" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="password" name="password" value="<?php echo $password['password']; ?>" required autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary show-password" type="button" onclick="togglePasswordVisibility()"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Descrizione:</label>
                    <textarea class="form-control" id="description" name="description" rows="3" autocomplete="off"><?php echo $password['description']; ?></textarea>
                </div>
                <div class="row ml-0">
                    <button type="submit" class="btn btn-primary mr-3">Aggiorna Password</button>
                    <a href="dashboard.php" class="btn btn-secondary">Torna alla Dashboard</a>
                </div>        
            </form>
        <?php } else { ?>
            <div class="alert alert-danger" role="alert">
                Password non trovata o non hai i permessi per modificarla.
            </div>
        <?php } ?>
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
