<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;

    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/entry.php';

        $entry = new Entry($db);
        $entry->id = isset($queryParam) ? $queryParam : die();
        $entry->readOne();

        foreach($data as $key => $value) {
            if ($key === 'status') {
                $entry->$key = (int)$data->$key;
            } else {
                $entry->$key = $data->$key;
            }
        }

        if ($entry->update()) {
            http_response_code(200);
            echo json_encode(array('message' => 'entry was updated.'));
        } else {
            http_response_code(503);
            echo json_encode(array('error' => 'Unable to update entry.'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }

?>