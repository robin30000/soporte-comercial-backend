<?php

require_once 'db.php';

class VisitasTerreno
{
    private $_DB;

    public function __construct()
    {
        $this->_DB = new DB;
    }

    public function consultaPedido($data)
    {

        //echo 8484848448;exit();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://10.100.66.254/visitas-terreno/api/ventaPedido/$data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $dataclick = json_decode($data, TRUE);

        if ($dataclick) {
            require_once 'Contador.php';
            $contador = new Contador();
            $contador->Contador('Venta-Instale');
        }

        echo json_encode($dataclick);

    }
}