<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/homeClient.php';

        $homeClient = new HomeClient($db);
        $homeClient->id = isset($queryParam) ? $queryParam : die();

        foreach($data as $key => $value) {
            $homeClient->$key = $data->$key;
        }

        if ($homeClient->update()) {
            http_response_code(200);
            echo json_encode(array('message' => 'homeClient was updated.'));
        } else {
            http_response_code(503);
            echo json_encode(array('error' => 'Unable to update homeClient.'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>