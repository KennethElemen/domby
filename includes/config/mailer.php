<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../phpmailer/src/Exception.php';
require __DIR__ . '/../phpmailer/src/PHPMailer.php';
require __DIR__ . '/../phpmailer/src/SMTP.php';

function sendEmail($recipient, $subject, $message) {
    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'belldorm21@gmail.com';
    $mail->Password = 'ydku xdny mubi sdoc';
    $mail->SMTPSecure = 'tls'; // Change to 'ssl' if needed
    $mail->Port = 587;

    $mail->setFrom('belldorm21@gmail.com');
    $mail->addAddress($recipient);

    $mail->Subject = $subject;
    $mail->msgHTML($message);

     if (!$mail->send()) {
        return false; // Email sending failed
    } else {
        return true; // Email sent successfully
    }
}
?>