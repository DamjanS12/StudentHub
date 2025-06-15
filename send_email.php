<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';
require_once 'app/models/db.php';

session_start();

function send_email($task, $recipient) {
    $smtp_host = 'smtp.gmail.com';
    $smtp_username = 'kti2172021@gmail.com';
    $smtp_password = '1209002'; 
    $smtp_port = 587;

    $mail = new PHPMailer(true);

    try {
        
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_username;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 'tls'
        $mail->Port       = $smtp_port;

       
        $mail->setFrom($_SESSION['email'], 'studenthub');
        foreach ($recipients as $email) {
            $mail->addAddress($email);
        }

        $mail->isHTML(true);
        $mail->Subject = 'New Task Assigned: ' . $task['title'];
        $mail->Body    = 'You have been assigned a new task: <strong>' . htmlspecialchars($task['title']) . '</strong><br>' .
                         'Description: ' . nl2br(htmlspecialchars($task['description'])) . '<br>' .
                         'Due Date: ' . htmlspecialchars($task['due_date']);

        $mail->send();
        echo 'Email sent successfully!';
    } catch (Exception $e) {
        echo "Email sending failed: {$mail->ErrorInfo}";
    }
}
