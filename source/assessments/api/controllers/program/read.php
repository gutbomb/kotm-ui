<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/program.php';
        $program = new Program($db);
        
        $stmt = $program->read();
        $num = $stmt->rowCount();
        
        if($num>0){
            $programs_arr=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $programItem=array(
                    'id' => $id,
                    'name' => $name
                );
                array_push($programs_arr, $programItem);
            }
            http_response_code(200);
            echo json_encode($programs_arr);
        } else {
            http_response_code(404);
            echo json_encode(
                array('error' => 'No programs found.')
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