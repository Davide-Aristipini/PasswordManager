<?php
require_once 'config.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'mail.sito.com'; // Inserisci il tuo server SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'noreply@sito.com'; // Inserisci il tuo indirizzo email per l'autenticazione SMTP
    $mail->Password = 'password'; // Inserisci la tua password per l'autenticazione SMTP
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('noreply@sito.com', 'SalvaPassword');
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;

    if ($mail->send()) {
        return true;
    } else {
        return false;
    }
}
?>
