<?php
// Include PHPMailer library files
require 'mail/phpmailer/src/PHPMailer.php';
require 'mail/phpmailer/src/SMTP.php';
require 'mail/phpmailer/src/Exception.php';

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
    $mail->Username = 'your-email@gmail.com';   // Your Gmail address
    $mail->Password = 'your-app-password';      // Your Gmail app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('noreply@yourdomain.com', 'Mailer'); // Sender's email address and name
    $mail->addAddress('yourname@yourdomain.com', 'Your Name'); // Add a recipient

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
