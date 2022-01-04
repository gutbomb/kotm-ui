<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

            
        include_once 'objects/homeClient.php';
        $homeClient = new HomeClient($db);
        $homeClient->id = isset($queryParam) ? $queryParam : die();
        $homeClient->readOne();
        
        if ($homeClient->id!=null){
            $homeClient_arr = array(
                'id' =>  $homeClient->id,
                'firstName' => $homeClient->firstName,
                'lastName' => $homeClient->lastName,
                'email' => $homeClient->email,
                'language' => $homeClient->language
            );
            http_response_code(200);
            echo json_encode($homeClient_arr);
        } else {
            http_response_code(404);
            echo json_encode(array('error' => 'homeClient does not exist.'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>