<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['method'])) {
    switch ($data['method']) {
        case 'VisitasTerreno':
            require_once '../Class/consultas.php';
            $user = new consultas();
            $user->visitasTerreno($data['data']);
            break;
        case 'EquiposInstalados':
            require_once '../Class/consultas.php';
            $user = new consultas();
            $user->equiposInstalados($data['data']);
            break;
        case 'Incompleto':
            require_once '../Class/consultas.php';
            $user = new consultas();
            $user->incompleto($data['data']);
            break;
        case 'Supervisor':
            require_once '../Class/consultas.php';
            $user = new consultas();
            $user->Supervisor($data['data']);
            break;
        default:
            echo 'ninguna opción valida.';
            break;
    }
} else {
    echo 'ninguna opción valida.';
}