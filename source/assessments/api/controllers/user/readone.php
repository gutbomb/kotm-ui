<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/user.php';
        $user = new User($db);
        $user->id = isset($queryParam) ? $queryParam : die();
        $user->readOne();
        if ($decoded->data->id === $user->id || $decoded->data->userLevel === 'admin' || $decoded->data->userLevel === 'superadmin') {
            if ($user->id!=null){
                $user_arr = array(
                    'id' =>  $user->id,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'email' => $user->email,
                    'passwordMustChange' => $user->passwordMustChange,
                    'currentAttest' => boolVal($user->currentAttest),
                    'userLevel' => $user->userLevel,
                    'lastLogin' => $user->lastLogin,
                    'created' => $user->created,
                    'enabled' => boolVal($user->enabled),
                    'programId' => $user->programId,
                    'programName' => $user->programName
                );
                http_response_code(200);
                echo json_encode($user_arr);
            } else {
                http_response_code(404);
                echo json_encode(array('error' => 'user does not exist.'));
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