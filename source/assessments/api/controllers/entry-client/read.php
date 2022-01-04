<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/entryClient.php';
        $entryClient = new EntryClient($db);
        
        $stmt = $entryClient->read();
        $num = $stmt->rowCount();
        
        if($num>0){
            $entryClients_arr=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $entryClientItem=array(
                    'id' => $id,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'color' => $color,
                    'number' => $number
                );
                array_push($entryClients_arr, $entryClientItem);
            }
            http_response_code(200);
            echo json_encode($entryClients_arr);
        } else {
            http_response_code(404);
            echo json_encode(
                array('error' => 'No entryClients found.')
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