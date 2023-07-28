
<?php
    // Inizializza la sessione (se non già inizializzata)
    session_start();

    // Verifica se l'utente è autenticato
    if (!isset($_SESSION['token'])) {
        // L'utente non è autenticato, reindirizzalo alla pagina di login
        header("Location: login.php");
        exit();
    }

    require_once 'config.php';
    

    // Recupera le password dell'utente dal database
    $token = $_SESSION['token'];
    $sql = "SELECT * FROM passwords WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    include 'header.php';
?>
    <div class="container mt-4">
        <div class="row">
            <div class="container row">
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <?php
                    $id = $row['id'];
                    $title = $row['title'];
                    $username = $row['username'];
                    $encryptedPassword = $row['password'];
                    $password = $encryptedPassword;
                    $description = $row['description'];
                    ?>
                    <div class="card mb-3 ml-2" style="width: 32%;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($title); ?></h5>
                            <p class="card-text"><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                            <p class="card-text"><strong>Password:</strong> <?php echo htmlspecialchars($password); ?></p>
                            <input type="hidden" value="<?php echo htmlspecialchars($password); ?>" id="password_<?php echo $id; ?>">
                            <p class="card-text"><strong>Descrizione:</strong> <?php echo htmlspecialchars($description); ?></p>
                            <a class="btn btn-sm btn-secondary mr-2" href="<?php echo 'edit_password.php?token='.$token.'&id='.$id;?>">Modifica</a>
                            <a class="btn btn-sm btn-danger" href="<?php echo 'delete.php?token='.$token.'&id='.$id;?>" onclick="return confirm('Sei sicuro di voler eliminare questa password?');">Elimina</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Card vuota con pulsante per aggiungere una nuova password -->
        <div class="container text-center">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Aggiungi una nuova password</h5>
                    <p class="card-text"><a class="btn btn-lg btn-success" href="new_password.php">+</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        // Funzione per mostrare o nascondere la password
        function togglePassword(inputId, iconId) {
            var passwordInput = document.getElementById(inputId);
            var passwordIcon = document.getElementById(iconId);

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordIcon.classList.remove("fa-eye");
                passwordIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                passwordIcon.classList.remove("fa-eye-slash");
                passwordIcon.classList.add("fa-eye");
            }
        }

        function editCard(cardId) {
            var passwordField = document.getElementById("password_" + cardId);
            var editBtn = document.getElementById("editBtn_" + cardId);

            if (passwordField.readOnly) {
                passwordField.readOnly = false;
                editBtn.innerHTML = "Salva";

                // Implementa la logica per abilitare la modifica degli altri campi della card (puoi utilizzare modali o altri metodi)
                // Aggiungi qui la logica per gestire la modifica della card
                console.log("Modifica card con ID: " + cardId);
            } else {
                passwordField.readOnly = true;
                editBtn.innerHTML = "Modifica";

                // Implementa la logica per salvare le modifiche apportate alla card (puoi utilizzare modali o altri metodi)
                // Aggiungi qui la logica per gestire il salvataggio delle modifiche
                console.log("Salva modifiche card con ID: " + cardId);
            }
        }

        $(document).ready(function() {
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if($('#show_hide_password input').attr("type") == "text"){
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass( "fa-eye-slash" );
                    $('#show_hide_password i').removeClass( "fa-eye" );
                }else if($('#show_hide_password input').attr("type") == "password"){
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass( "fa-eye-slash" );
                    $('#show_hide_password i').addClass( "fa-eye" );
                }
            });
        });
    </script>
</body>
</html>
