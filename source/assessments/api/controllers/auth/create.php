<?php
    include_once 'objects/user.php';
    use \Firebase\JWT\JWT;

    $user = new User($db);
    if(
        !empty($data->email) &&
        !empty($data->password)
    ) {
        $user->email = $data->email;
        $user->password = $data->password;
        if ($user->auth()) {
            $secret_key = "KOTMJWTAPP";
            $issuer_claim = "KOTM"; // this can be the servername
            $issuedat_claim = time();
            $notbefore_claim = $issuedat_claim; //not before in seconds
            $expire_claim = $issuedat_claim + 86400; // expire time in seconds
            $token = array(
                "iss" => $issuer_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => $user
            );
            http_response_code(200);
            $jwt = JWT::encode($token, $secret_key);
            echo json_encode(
                array(
                    "message" => "Successful login.",
                    "jwt" => $jwt,
                    "expireAt" => $expire_claim
                ));
        } else {
            http_response_code(401);
            echo json_encode(array('error' => 'invalid username or password'));
        }
    } else {
        http_response_code(401);
        echo json_encode(array('error' => 'incomplete data'));
    }
?>