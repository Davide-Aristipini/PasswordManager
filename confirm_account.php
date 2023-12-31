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
    <title>Conferma Account</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
require_once 'config.php';
require_once 'PHPGangsta/GoogleAuthenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Verifica se l'utente non è ancora confermato e se il token corrisponde
    $sql = "SELECT qr_code_secret FROM users WHERE email = ? AND token = ? AND is_confirmed = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $secret = $ga->createSecret();

        // Genera il codice QR per Google Authenticator
        $qrCodeUrl = $ga->getQRCodeGoogleUrl('SalvaPassword', $secret);

        // Visualizza il codice QR e un modulo per l'inserimento del codice TOTP
        echo '<div class="container text-center">';
        echo '<h1>Conferma il tuo account</h1>';
        echo '<p>Scansiona il seguente codice QR con l\'app Google Authenticator:</p>';
        echo '<img src="' . $qrCodeUrl . '" alt="QR Code">';
        echo '<form action="verify_2fa.php" method="post" class="form-inline justify-content-center mt-3" id="verifyForm">
                <div class="form-group">
                  <input type="hidden" id="secret" name="secret" value="' . $secret . '">
                  <input type="hidden" id="email" name="email" value="' . $email . '">
                </div>';

        for($i = 0; $i < 6; $i++) {
            echo '<input type="number" inputmode="numeric" pattern="[0-9]*" id="code' . $i . '" name="code[]" class="form-control input-code" min="0" max="9" maxlength="1" oninput="validity.valid||(value=\'\');" required>';
        }

        echo '<div class="container text-center">';
        echo '<button type="submit" class="btn btn-primary mt-2">Conferma</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    } else {
        echo '<div class="container">';
        echo '<p class="text-danger">Link di conferma non valido o scaduto.</p>';
        echo '</div>';
    }
} else {
    echo '<div class="container">';
    echo '<p class="text-danger">Link di conferma non valido o mancante.</p>';
    echo '</div>';
}

$conn->close();
?>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Auto focus on the first input field
        $('#code0').focus();

        // Handle input and automatically move cursor to next input
        $('.input-code').keyup(function(e) {
            if($(this).val().length == $(this).attr('maxlength')) {
                $(this).next('.input-code').focus();
            }
        });

        // Combine all inputs into a single one before submitting
        $('#verifyForm').submit(function(e) {
            let code = '';
            $('.input-code').each(function() {
                code += $(this).val();
            });

            $('<input>').attr({
                type: 'hidden',
                id: 'code',
                name: 'code',
                value: code
            }).appendTo('#verifyForm');
        });

        var codeInput = $('input[name="code[]"]');
        codeInput.keyup(function(e) {
            if (e.keyCode == 8 || e.keyCode == 46) {
                // Backspace o Delete premuto
                $(this).val(''); // Cancella il carattere corrente
                $(this).prev('input').focus(); // Passa al campo di input precedente
            } else if (e.keyCode == 13) {
                // Invio premuto
                var allFilled = true;
                codeInput.each(function() {
                    if ($(this).val() == '') {
                        allFilled = false;
                    }
                });

                if (allFilled) {
                    // Se tutti i campi sono riempiti, invia il form
                    $('#form-2fa').submit();
                }
            } else {
                // Qualsiasi altro tasto premuto
                $(this).next('input').focus(); // Passa al campo di input successivo
            }
        });
    });
</script>

</body>
</html>
