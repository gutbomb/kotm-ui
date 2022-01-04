<?php
    include_once 'objects/homeResponse.php';
    include_once 'objects/user.php';
    include_once 'objects/homeClient.php';
    $homeResponse = new HomeResponse($db);
    $homeResponse->link = isset($queryParam) ? $queryParam : die();
    $homeResponse->readOne();
    
    if ($homeResponse->sessionDate!=null){
        $staff = new User($db);
        $client = new HomeClient($db);
        $staff->id = $homeResponse->staffId;
        $client->id = $homeResponse->clientId;
        $staffStmt = $staff->readOne(true);
        $clientStmt = $client->readOne();
        $homeResponse_arr = array(
            'id' =>  $homeResponse->id,
            'sessionDate' => $homeResponse->sessionDate,
            'client' => $client,
            'staff' => $staff,
            'affirm' => boolval($homeResponse->affirm),
            'sentDate' => $homeResponse->sentDate,
            'responseDate' => $homeResponse->responseDate,
            'notes' => $homeResponse->notes,
            'notesDate' => $homeResponse->notesDate,
            'link' => $homeResponse->link
        );
        if ($homeResponse_arr['responseDate'] === NULL) {
            $homeResponse_arr['affirm'] = NULL;
        }
        http_response_code(200);
        echo json_encode($homeResponse_arr);
    } else {
        http_response_code(404);
        echo json_encode(array('error' => 'homeResponse does not exist.'));
    }
?>