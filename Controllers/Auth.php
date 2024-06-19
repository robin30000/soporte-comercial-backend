<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

require_once '../Class/Auth.php';
$data = json_decode(file_get_contents("php://input"), true);
$Auth = new Auth;

if (isset($data['method'])) {
    switch ($data['method']) {
        case 'Login':
            $Auth->Login($data['data']);
            break;
        case 'SolicitaAcceso':
            $res = $Auth->SolicitaAcceso($data['data']);
            echo json_encode($res);
            break;
    }
}


