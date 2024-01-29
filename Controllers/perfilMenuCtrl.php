<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');


$data = json_decode(file_get_contents("php://input"), true);


if (isset($data['method'])) {
    switch ($data['method']) {
        case 'listMenu':
            require_once '../Class/PerfilMenu.php';
            $usuario = new PerfilMenu();
            $res = $usuario->listMenu();
            echo json_encode($res);
            break;
        case 'listPerfil':
            require_once '../Class/PerfilMenu.php';
            $usuario = new PerfilMenu();
            $res = $usuario->listPerfil();
            echo json_encode($res);
            break;
        case 'editaMenu':
            require_once '../Class/PerfilMenu.php';
            $usuario = new PerfilMenu();
            $res = $usuario->editaMenu($data['data']);
            echo json_encode($res);
            break;
        case 'editaPerfil':
            require_once '../Class/PerfilMenu.php';
            $usuario = new PerfilMenu();
            $res = $usuario->editaPerfil($data['data']);
            echo json_encode($res);
            break;
        case 'cambiaEstadoMenu':
            require_once '../Class/PerfilMenu.php';
            $usuario = new PerfilMenu();
            $res = $usuario->cambiaEstadoMenu($data['data']);
            echo json_encode($res);
            break;
    }
}