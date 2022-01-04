<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        if ($decoded->data->userLevel === 'admin' || $decoded->data->userLevel === 'superadmin') {

            include_once 'objects/entry.php';
            $entry = new Entry($db);
            
            $stmt = $entry->read();
            $num = $stmt->rowCount();
            
            if($num>0){
                $entries_arr=array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $entryItem=array(
                        'id' => $id,
                        'date' => $date,
                        'status' => boolval($status),
                        'color' => $color,
                        'number' => $number
                    );
                    array_push($entries_arr, $entryItem);
                }
                http_response_code(200);
                echo json_encode($entries_arr);
            } else {
                http_response_code(404);
                echo json_encode(
                    array('error' => 'No entries found.')
                );
            }
        } else {
            http_response_code(403);
            echo json_encode(array('message' => 'Access Denied'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>