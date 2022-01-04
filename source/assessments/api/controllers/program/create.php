<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;

    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/entryClient.php';
        $entryClient = new EntryClient($db);

        if(
            !empty($data->firstName) &&
            !empty($data->lastName) &&
            !empty($data->color) &&
            !empty($data->number)
        ){
        
            $entryClient->firstName = $data->firstName;
            $entryClient->lastName = $data->lastName;
            $entryClient->color = $data->color;
            $entryClient->number = $data->number;

            if ($entryClient->create()) {
                http_response_code(201);
                echo json_encode(array('message' => "entryClient was created."));
            } else {
                http_response_code(503);
                echo json_encode(array('error' => 'Unable to create entryClient.'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('error' => 'Unable to create entryClient. Data is incomplete.'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>