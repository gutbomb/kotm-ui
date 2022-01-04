<?php
    include 'protect.php';
    use \Firebase\JWT\JWT;
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        if ($decoded->data->userLevel === 'admin' || $decoded->data->userLevel === 'superadmin') {
            include_once 'objects/user.php';
            $user = new User($db);
            if($decoded->data->userLevel !== 'superadmin') {
                $programId = $decoded->data->programId;
            } else {
                $programId = false;
            }
            $stmt = $user->read($programId);
            $num = $stmt->rowCount();
            
            if($num>0){
                $users_arr=array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $statusHistory = array();
                    $attestStmt = $user->getAttestHistory($id);
                    $attestNum = $attestStmt->rowCount();
                    if($attestNum>0){
                        while($attestRow = $attestStmt->fetch(PDO::FETCH_ASSOC)){
                            extract($attestRow);
                            $attestItem = array(
                                'status' => boolval($historicalStatus),
                                'date' => $date
                            );
                            array_push($statusHistory, $attestItem);
                        }
                    }
                    $userItem=array(
                        'id' => $id,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'email' => $email,
                        'created' => $created,
                        'lastLogin' => $lastLogin,
                        'status' => boolval($status),
                        'statusDate' => $statusDate,
                        'userLevel' => $userLevel,
                        'enabled' => boolval($enabled),
                        'programId' => strval($programId),
                        'programName' => $programName,
                        'statusHistory' => $statusHistory
                    );
                    array_push($users_arr, $userItem);
                }
                http_response_code(200);
                echo json_encode($users_arr);
            } else {
                http_response_code(404);
                echo json_encode(
                    array('error' => 'No users found.')
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