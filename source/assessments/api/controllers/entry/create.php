<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;

    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/entry.php';
        $entry = new Entry($db);
        if(
            !empty($data->color) &&
            !empty($data->number)
        ){

            $entry->color = $data->color;
            $entry->number = $data->number;
            $entry->status = $data->status;

            if ($entry->create()){
                http_response_code(201);
                echo json_encode(array('message' => 'Entry was created.'));
            } else {
                http_response_code(503);
                echo json_encode(array('error' => 'Unable to create entry.'));
            }
        } else {
            http_response_code(503);
            echo json_encode(array('error' => 'Unable to create entry.'));
        }

    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>