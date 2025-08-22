<?php

require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public static function sendRegisterEmail($to, $verification_code)
    {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER'];
            $mail->Password = $_ENV['MAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom($mail->Username, 'Roster System');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = 'Your 6-Digit Register Verification Code';
            $mail->Body = "<h1>Your code is: <strong>{$verification_code}</strong></h1>
                           <p>It expires in 10 minutes.</p>";
            return $mail->send();
        } catch(Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo . " - " . $e->getMessage());
            return false;
        }
    }

    public static function sendResetPasswordEmail($to, $reset_link)
    {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'opchitc@gmail.com';
            $mail->Password = 'yyfymzkmnmvagwta';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom($mail->Username, 'Roster System');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "<h1>Reset your password</h1>
                           <p>Click the link below to reset your password:</p>
                           <a href='{$reset_link}'>Reset Password</a>
                           <p>This link will expire in 60 minutes.</p>";
            return $mail->send();
        } catch(Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo . " - " . $e->getMessage());
            return false;
        }
    }
}