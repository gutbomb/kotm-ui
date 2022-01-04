<?php
    include_once 'objects/homeResponse.php';
    include_once 'objects/user.php';
    include_once 'objects/homeClient.php';

    require 'phpmailer/Exception.php';
    require 'phpmailer/PHPMailer.php';
    require 'phpmailer/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    $homeResponse = new HomeResponse($db);
    $homeResponse->link = isset($queryParam) ? $queryParam : die();
    $homeResponse->readOne();

    $staff = new User($db);
    $client = new HomeClient($db);
    $staff->id = $homeResponse->staffId;
    $client->id = $homeResponse->clientId;
    $staffStmt = $staff->readOne(true);
    $clientStmt = $client->readOne();

    foreach($data as $key => $value) {
        if ($key === 'affirm') {
            $affirmChanged = true;
            $homeResponse->$key = (int)$data->$key;
        } else {
            $homeResponse->$key = $data->$key;
        }
    }

    // echo json_encode($homeResponse);

    if ($homeResponse->update()) {
        if($affirmChanged) {
            if($homeResponse->affirm) {
                $affirmMessage = "YES - These statements are true and correct to the best of my knowledge.\nPlease plan to attend this appointment.";
            } else {
                $affirmMessage = "NO - These statements are true and correct to the best of my knowledge.\nPlease cancel this appointment and contact your supervisor and the client to let them know.";
            }
            

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
            $mail->Subject = 'KOTM Health Questionnaire Response';
            $mail->Body    = "$client->firstName $client->lastName with an appointment scheduled for $homeResponse->sessionDate has responded to the health questionnaire with the response:\n$affirmMessage";
            $mail->addAddress($staff->email);
            $mail->send();
        }
        http_response_code(200);
        echo json_encode(array('message' => 'homeResponse was updated.'));
    } else {
        http_response_code(503);
        echo json_encode(array('error' => 'Unable to update homeResponse.'));
    }

?>