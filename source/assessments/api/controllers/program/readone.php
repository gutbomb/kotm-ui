<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

            
        include_once 'objects/entryClient.php';
        $entryClient = new EntryClient($db);
        $entryClient->id = isset($queryParam) ? $queryParam : die();
        $entryClient->readOne();
        
        if ($entryClient->id!=null){
            $entryClient_arr = array(
                'id' =>  $entryClient->id,
                'firstName' => $entryClient->firstName,
                'lastName' => $entryClient->lastName,
                'color' => $entryClient->color,
                'number' => $entryClient->number
            );
            http_response_code(200);
            echo json_encode($entryClient_arr);
        } else {
            http_response_code(404);
            echo json_encode(array('error' => 'entryClient does not exist.'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>