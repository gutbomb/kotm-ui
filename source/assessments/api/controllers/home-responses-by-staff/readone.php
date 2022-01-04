<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/homeResponse.php';
        include_once 'objects/user.php';
        include_once 'objects/homeClient.php';
        $homeResponse = new HomeResponse($db);
        $staffId = isset($queryParam) ? $queryParam : die();
        $stmt = $homeResponse->responsesByStaff($staffId);
        $num = $stmt->rowCount();
        
        if($num>0){
            $homeResponses_arr=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $staff = new User($db);
                $client = new HomeClient($db);
                $staff->id = $staffId;
                $client->id = $clientId;
                $staffStmt = $staff->readOne(true);
                $clientStmt = $client->readOne();
                $responseItem=array(
                    'id' => $id,
                    'sessionDate' => $sessionDate,
                    'affirm' => boolval($affirm),
                    'responseDate' => $responseDate,
                    'notes' => $notes,
                    'notesDate' => $notesDate,
                    'staff' => $staff,
                    'client' => $client,
                    'link' => $link
                );
                array_push($homeResponses_arr, $responseItem);
            }
            http_response_code(200);
            echo json_encode($homeResponses_arr);
        } else {
            http_response_code(404);
            echo json_encode(
                array('error' => 'No homeResponses found.')
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