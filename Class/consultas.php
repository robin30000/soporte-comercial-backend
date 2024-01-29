<?php
error_reporting(0);
require_once 'db.php';

class consultas
{
    private $_DB;

    public function __construct()
    {
        $this->_DB = new DB;
    }

    public function visitasTerreno($data)
    {
        if ($data['cedula'] === 'vacio') {
            require_once 'Contador.php';
            $contador = new Contador();
            $contador->Contador('Gescom');
        } else {
            require_once 'Contador.php';
            $contador = new Contador();
            $contador->Contador('VisitasTerreno');
        }

        $pedido = $data['pedido'];
        $cedula = $data['cedula'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://10.100.66.254/visitas-terreno/api/visitas-terreno/$pedido/$cedula");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        $dataclick = json_decode($data, TRUE);
        echo json_encode($dataclick);
    }

    public function equiposInstalados($data)
    {
        require_once 'Contador.php';
        $contador = new Contador();
        $contador->Contador('Consulta-equipos-instalados');

        $pedido = $data['pedido'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://10.100.66.254/visitas-terreno/api/equipos/$pedido");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        $dataclick = json_decode($data, TRUE);
        echo json_encode($dataclick);
    }

    public function incompleto($data)
    {
        $pedido = $data['pedido'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://10.100.66.254/visitas-terreno/api/incompleto/$pedido");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        $dataclick = json_decode($data, TRUE);
        echo json_encode($dataclick);
    }

    public function Supervisor($data)
    {
        $cedula = $data['cedula'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://10.100.66.254/visitas-terreno/api/supervisor/$cedula");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        $dataclick = json_decode($data, TRUE);
        echo json_encode($dataclick);
    }

}