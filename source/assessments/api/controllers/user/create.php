<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;

    require 'phpmailer/Exception.php';
    require 'phpmailer/PHPMailer.php';
    require 'phpmailer/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        if ($decoded->data->userLevel === 'admin' || $decoded->data->userLevel === 'superadmin') {
            include_once 'objects/user.php';
            $user = new User($db);

            if(
                !empty($data->firstName) &&
                !empty($data->lastName) &&
                !empty($data->email)
            ){
            
                $user->firstName = $data->firstName;
                $user->lastName = $data->lastName;
                $user->email = $data->email;
                $user->programId = $data->programId;
                $user->password = randomPassword();
            
                if ($user->create()){
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
                    $mail->isHTML(true);
                    $mail->Subject = 'KOTM Health Questionnaire App Account';
                    $mail->Body    = "$user->firstName $user->lastName,<br>An account has been created for you for the KOTM Health Questionnaire App.  This application is used by KOTM Staff to complete their own daily health questionnaire for in-office and client-home work.  This app is also used to send health questionnaire requests to families for in-home and in-clinic appointments.<br><br>Your username is <b>$user->email</b> and your temporary password is <b>$user->password</b><br><br>When you first log in you will be asked to change your password.  You can access the application at <a href=\"$environment[siteUrl]/assessments/\">$environment[siteUrl]/assessments/</a><br><br>Thank You,<br>Kids On The Move";
                    $mail->addAddress($user->email);
                    $mail->send();

                    http_response_code(201);
                    echo json_encode(array('message' => "user was created."));
                } else {
                    http_response_code(503);
                    echo json_encode(array('error' => 'Unable to create user.'));
                }
            } else {
                http_response_code(400);
                echo json_encode(array('error' => 'Unable to create user. Data is incomplete.'));
            }
        } else {
            http_response_code(403);
            echo json_encode(array('message' => 'Access Denied'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>