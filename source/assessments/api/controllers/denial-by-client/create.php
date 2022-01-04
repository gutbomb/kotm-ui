<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;

    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/entry.php';
        $entry = new Entry($db);
        
        $entry->getLatestDenial($data->color, $data->number);
        if($entry->date) {
            $entry_arr = array(
                'date' => $entry->date
            );
            http_response_code(200);
            echo json_encode($entry_arr);
        } else {
            http_response_code(404);
            echo json_encode(
                array('error' => 'No entries found.')
            );
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>