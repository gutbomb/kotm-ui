<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;

    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        include_once 'objects/user.php';

        $user = new User($db);
        $user->email = $data->email;
        $user->password = $data->password;
        $user->newPassword = $data->newPassword;
        if ($decoded->data->email === $user->email) {
            if($user->changePassword()) {
                http_response_code(200);
                echo json_encode(array('message' => "Password changed successfully"));
            } else {
                http_response_code(503);
                echo json_encode(array('error' => 'Unable to change password.'));
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