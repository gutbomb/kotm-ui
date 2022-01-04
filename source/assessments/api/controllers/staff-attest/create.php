<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;

    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/user.php';
        $user = new User($db);

        if(
            !empty($data->userId)
        ){
        
            $user->id = $data->userId;
            $affirm = (int)$data->affirm;
            
            if ($user->staffAttest($affirm)){
                http_response_code(201);
                echo json_encode(array('message' => "response was sent."));
            } else {
                http_response_code(503);
                echo json_encode(array('error' => 'Unable to send response.'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('error' => 'Unable to send response. Data is incomplete.'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>