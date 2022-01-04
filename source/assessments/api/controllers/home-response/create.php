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
        include_once 'objects/homeResponse.php';
        include_once 'objects/user.php';
        include_once 'objects/homeClient.php';
        $homeResponse = new HomeResponse($db);

        if(
            !empty($data->sessionDate) &&
            !empty($data->clientId) &&
            !empty($data->staffId)
        ){
        
            $homeResponse->sessionDate = $data->sessionDate;
            $homeResponse->clientId = $data->clientId;
            $homeResponse->staffId = $data->staffId;
            $staff = new User($db);
            $client = new HomeClient($db);
            $staff->id = $homeResponse->staffId;
            $client->id = $homeResponse->clientId;
            $staffStmt = $staff->readOne(true);
            $clientStmt = $client->readOne();
            
            $link = $homeResponse->create();
            if ($link){
                include 'config/phpmailer.php';
                $mail = new PHPMailer(true);
                $mail->SMTPOptions = $mailSMTPOptions;
                $mail->CharSet    = 'UTF-8';
                $mail->isSMTP();
                $mail->Host       = $mailSMTPHost;
                $mail->SMTPAuth   = true;
                $mail->Username   = $mailAccount;
                $mail->Password   = $mailPassword;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = $mailSMTPPort;
                $mail->setFrom($mailAddress);
                $mail->addReplyTo('DoNotReply@kotm.org');
                $mail->isHTML(true);
                if ($client->language === 'es') {
                    $mail->Subject = 'Evaluación de salud de KOTM (obligatoria)';
                } else {
                    $mail->Subject = 'KOTM Health Questionnaire - Required';
                }
                if ($client->language === 'es') {
                    $IntlDateFormatter = new IntlDateFormatter(
                        'es_US',
                        IntlDateFormatter::FULL,
                        IntlDateFormatter::SHORT
                    );
                    $mailDate = $IntlDateFormatter->format(strtotime($homeResponse->sessionDate));
                } else {
                    $mailDate = date('l, F j \a\t g\:i a', strtotime($homeResponse->sessionDate));
                }
                $mailLink = "$environment[siteUrl]/assessments/#!/assessment/$link";
                if ($client->language === 'es') {
                    $mail->Body    = "$client->firstName $client->lastName,<br>Tiene una cita con <b>$staff->firstName $staff->lastName</b> programada para el <b>$mailDate</b>.  Para poder tener la cita, debe completar la <a href=\"$mailLink\">Evaluación de salud de KOTM</a>. Si la evaluación no se completa <span style=\"color: red; font-weight:bold\">antes del horario de la cita</span>, esta podría tener que reprogramarse.<br><br>Haga clic en el enlace a continuación (o cópielo y péguelo en su navegador) para completar esta evaluación:<br><br> <a href=\"$mailLink\">$mailLink</a><br><br>¡Gracias por ayudarnos a prevenir la propagación de la COVID-19!<br><br>Kids On The Move";
                } else {
                    $mail->Body    = "$client->firstName $client->lastName,<br>You have an appointment with <b>$staff->firstName $staff->lastName</b> scheduled for <b>$mailDate</b>.  Before your appointment can take place, you will need to complete the <a href=\"$mailLink\">KOTM Health Questionnaire</a>. If this questionnaire is not completed <span style=\"color: red; font-weight:bold\">prior to your appointment time</span>, your appointment may need to be rescheduled.<br><br>Please click on the following link (or copy and paste into a browser) to complete this questionnaire:<br><br> <a href=\"$mailLink\">$mailLink</a><br><br>Thank you for helping us prevent the spread of COVID-19!<br><br>Kids On The Move";
                }
                $mail->addAddress($client->email);
                $mail->send();
                http_response_code(201);
                echo json_encode(array('message' => 'Response was created.'));
            } else {
                http_response_code(503);
                echo json_encode(array('error' => 'Unable to create response.'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('error' => 'Unable to create response. Data is incomplete.'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>