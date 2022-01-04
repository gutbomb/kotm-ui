<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/homeClient.php';
        $homeClient = new HomeClient($db);
        $staffId = isset($queryParam) ? $queryParam : die();
        $stmt = $homeClient->clientsByStaff($staffId);
        $num = $stmt->rowCount();
        
        if($num>0){
            $homeClients_arr=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $homeClientItem=array(
                    'id' => $id,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'email' => $email,
                    'language' => $language
                );
                array_push($homeClients_arr, $homeClientItem);
            }
            http_response_code(200);
            echo json_encode($homeClients_arr);
        } else {
            http_response_code(404);
            echo json_encode(
                array('error' => 'No homeClients found.')
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