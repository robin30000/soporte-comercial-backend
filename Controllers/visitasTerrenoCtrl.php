<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');


$data = json_decode(file_get_contents("php://input"),true);


if(isset($data['method'])){
    switch ($data['method']) {
        case 'consultaPedido':
            require_once '../Class/VisitasTerreno.php';
            $Visita = new VisitasTerreno;
            $Visita->consultaPedido($data['data']);
            break;
    }
}


