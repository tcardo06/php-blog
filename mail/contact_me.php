<?php
// Include PHPMailer library files
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';
require __DIR__ . '/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check for empty fields
if(empty($_POST['name']) ||
   empty($_POST['email']) ||
   empty($_POST['message']) ||
   !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo "No arguments Provided!";
    return false;
}

$name = strip_tags(htmlspecialchars($_POST['name']));
$email_address = strip_tags(htmlspecialchars($_POST['email']));
$phone = isset($_POST['phone']) ? strip_tags(htmlspecialchars($_POST['phone'])) : '';
$message = strip_tags(htmlspecialchars($_POST['message']));

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';             // Set the SMTP server to send through
    $mail->SMTPAuth = true;
    $mail->Username = 'tcardo0606@gmail.com';   // Your Gmail address
    $mail->Password = 'vieo mqez ejyn xqyw';      // Your Gmail app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('noreply@yourdomain.com', 'Mailer'); // Sender's email address and name
    $mail->addAddress('tcardo0606@gmail.com', 'Tom'); // Add a recipient

    // Content
    $mail->isHTML(true);                                  
    $mail->Subject = "Website Contact Form:  $name";
    $mail->Body    = "You have received a new message from your website contact form.<br><br>" .
                     "Here are the details:<br><br>" .
                     "Name: $name<br><br>Email: $email_address<br><br>" .
                     "Phone: $phone<br><br>Message:<br>$message";
    $mail->AltBody = "You have received a new message from your website contact form.\n\n" .
                     "Here are the details:\n\n" .
                     "Name: $name\n\nEmail: $email_address\n\n" .
                     "Phone: $phone\n\nMessage:\n$message";

    // Send email
    $mail->send();
    echo 'Message has been sent';
    return true;
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    return false;
}
?>
