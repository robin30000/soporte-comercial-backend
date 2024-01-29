<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['method'])) {
    switch ($data['method']) {
        case 'guardaPedidoVentaInstale':
            require_once '../Class/ventaInstale.php';
            $user = new ventaInstale();
            $user->guardaPedidoVentaInstale($data['data']);
            break;
        case 'respuestasPedidos':
            require_once '../Class/ventaInstale.php';
            $user = new ventaInstale();
            $user->respuestasPedidos($data['data']);
            break;
        case 'documento_tecnico':
            require_once '../Class/ventaInstale.php';
            $user = new ventaInstale();
            $user->documento_tecnico($data['data']);
            break;
        case 'observaciones':
            require_once '../Class/ventaInstale.php';
            $user = new ventaInstale();
            $user->observaciones();
            break;
        case 'export':
            require_once '../Class/ventaInstale.php';
            $user = new ventaInstale();
            $user->export($data['data']);
            break;
        default:
            echo 'ninguna opción valida.';
            break;
    }
} else {
    echo 'ninguna opción valida.';
}
