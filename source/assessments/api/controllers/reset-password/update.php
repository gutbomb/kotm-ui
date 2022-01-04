<?php
    include_once 'objects/user.php';
    $user = new User($db);
    $user->email = isset($queryParam) ? $queryParam : die();

    $resetPassword = $user->resetPassword();
    
    require 'phpmailer/Exception.php';
    require 'phpmailer/PHPMailer.php';
    require 'phpmailer/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    include 'config/phpmailer.php';

    $mail = new PHPMailer(true);
    $mail->SMTPOptions = $mailSMTPOptions;
    $mail->isSMTP();
    $mail->Host       = $mailSMTPHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $mailAccount;
    $mail->Password   = $mailPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $mailSMTPPort;
    $mail->setFrom($mailAddress);
    $mail->addReplyTo($mailAddress);
    $mail->isHTML(false);
    $mail->Subject = 'KOTM Health Questionnaire App Password Reset';
    $mail->Body    = "Your password has been reset to $resetPassword\n You can log in at $environment[siteUrl]/assessments/";
    $mail->addAddress($user->email);

    if ($resetPassword) {
        if($resetPassword === 'notfound') {
            http_response_code(200);
            echo json_encode(array('message' => "If you have an account in our system a password reset request has been sent to you."));
        } else {
            $mail->send();
            http_response_code(200);
            echo json_encode(array('message' => "If you have an account in our system a password reset request has been sent to you."));
        }
    } else {
        http_response_code(503);
        echo json_encode(array('error' => 'Unable to reset password.'));
    }
?>