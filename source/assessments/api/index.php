<?php
    include_once 'config/environment.php';
    include_once 'config/database.php';
    require_once 'jwt/BeforeValidException.php';
    require_once 'jwt/ExpiredException.php';
    require_once 'jwt/SignatureInvalidException.php';
    require_once 'jwt/JWT.php';



    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    $request = explode('/', $_SERVER['REQUEST_URI']);
    $controller = "controllers/$request[3]";
    
    if(isset($request[4])) {
        $queryParam = $request[4];
    }
    
    if(isset($request[5])) {
        $queryParam2 = $request[5];
    }
    
    $database = new Database();
    $db = $database->getConnection();
    $data = json_decode(file_get_contents('php://input'));

    switch($_SERVER['REQUEST_METHOD']) {
        case 'GET' :
            header('Access-Control-Allow-Methods: GET');
            if(isset($queryParam)) {
                include("$controller/readone.php");
            } else {
                include("$controller/read.php");
            }
            break;
        
        case 'POST' :
            header('Access-Control-Allow-Methods: POST');
            include("$controller/create.php");
            break;
        
        case 'PUT' :
            header('Access-Control-Allow-Methods: PUT');
            include("$controller/update.php");
            break;

        case 'DELETE' :
            header('Access-Control-Allow-Methods: DELETE');
            include("$controller/delete.php");
            break;
    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
?>