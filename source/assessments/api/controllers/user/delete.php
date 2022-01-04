<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/user.php';
        $user = new User($db);
        $user->id = isset($queryParam) ? $queryParam : die();

        if ($user->delete()){
            http_response_code(200);
            echo json_encode(array('message' => 'user was deleted.'));
        } else {
            http_response_code(503);
            echo json_encode(array('error' => 'Unable to delete user.'));
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied",
            "error" => $e->getMessage()
        ));
    }
?>