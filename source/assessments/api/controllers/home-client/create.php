<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;

    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/homeClient.php';
        $homeClient = new HomeClient($db);

        if(
            !empty($data->firstName) &&
            !empty($data->lastName) &&
            !empty($data->email) &&
            !empty($data->language)
        ){
        
            $homeClient->firstName = $data->firstName;
            $homeClient->lastName = $data->lastName;
            $homeClient->email = $data->email;
            $homeClient->language = $data->language;
            
            $id = $homeClient->create();
            if ($id) {
                http_response_code(201);
                echo json_encode(array('message' => "homeClient was created.", 'id' => $id));
            } else {
                http_response_code(503);
                echo json_encode(array('error' => 'Unable to create homeClient.' . $id));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('error' => 'Unable to create homeClient. Data is incomplete.'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
    
?>