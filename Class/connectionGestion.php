<?php

date_default_timezone_set('America/Bogota');

class Connection extends PDO
{
    private $tipo_de_base = 'sqlsrv';
    private $host = '10.100.74.235,1439';
    private $nombre_de_base = 'Gestion_Operativa';
    private $usuario = 'usrGestion_Operativa';
    private $contrasena = '4WgUYXmWGohcuO9b9QIg';

    public function __construct()
    {
        try {
            parent::__construct("$this->tipo_de_base:Server=$this->host;Database=$this->nombre_de_base", $this->usuario, $this->contrasena);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
            exit;
        }
    }
}

$con = new Connection();
//$stmt = $con->prepare("SELECT top 20 * FROM Agendamiento");
$stmt = $con->prepare("SELECT
                                MOTIVE_ID,
                                HORA_CITA,
                                FECHA_CITA 
                            FROM
                                FacInstalaciones_Pendientes_ETP 
                            WHERE
                                FECHA_CITA IS NOT NULL");
$stmt->execute();
$response = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($response);
