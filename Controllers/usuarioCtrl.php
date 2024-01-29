<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');


$data = json_decode(file_get_contents("php://input"), true);


if (isset($data['method'])) {
    switch ($data['method']) {
        case 'guarda-usuario':
            require_once '../Class/Usuario.php';
            $usuario = new Usuario();
            $res = $usuario->creaUsuario($data['data']);
            echo json_encode($res);
            break;
        case 'editaUsuario':
            require_once '../Class/Usuario.php';
            $usuario = new Usuario();
            $res = $usuario->editaUsuario($data['data']);
            echo json_encode($res);
            break;
        case 'deleteUsuario':
            require_once '../Class/Usuario.php';
            $usuario = new Usuario();
            $res = $usuario->deleteUsuario($data['data']);
            echo json_encode($res);
            break;
        case 'listUsuario':
            require_once '../Class/Usuario.php';
            $usuario = new Usuario();
            $res = $usuario->listUsuario($data['data']);
            echo json_encode($res);
            break;
    }
}